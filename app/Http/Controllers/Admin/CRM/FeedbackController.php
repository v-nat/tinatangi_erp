<?php

namespace App\Http\Controllers\Admin\CRM;

use Exception;
use Illuminate\Http\Request;
use App\Models\ServiceFeedback;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Location\Facades\Location;
use App\Http\Requests\StoreServiceFeedbackRequest;
use App\Http\Requests\UpdateFeedbackStatusRequest;

class FeedbackController extends Controller
{
    public function fetchFeedbacks()
    {
        try {
            $feedbacks = ServiceFeedback::latest()->paginate(15);

            return response()->json($feedbacks);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function submitFeedback(StoreServiceFeedbackRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $ip = $request->ip();
            $validatedData['ip_address'] = $ip;

            $userIp = in_array($ip, ['127.0.0.1', '::1']) ? '8.8.8.8' : $ip;

            if ($locationData = Location::get($userIp)) {
                $validatedData['location'] = "{$locationData->cityName}, {$locationData->regionName}, {$locationData->countryName}, {$locationData->postalCode}";
            } else {
                $validatedData['location'] = 'Location not found';
            }

            if ($request->hasFile('photo')) {
                $imageFile = $request->file('photo');
                $sourcePath = $imageFile->getRealPath();

                list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

                switch ($sourceType) {
                    case IMAGETYPE_JPEG:
                        $sourceImage = imagecreatefromjpeg($sourcePath);
                        break;
                    case IMAGETYPE_PNG:
                        $sourceImage = imagecreatefrompng($sourcePath);
                        break;
                    case IMAGETYPE_GIF:
                        $sourceImage = imagecreatefromgif($sourcePath);
                        break;
                    default:
                        return response()->json(['message' => 'Unsupported image type.'], 422);
                }

                $targetWidth = 1024;
                $targetHeight = 768;

                $sourceRatio = $sourceWidth / $sourceHeight;
                $targetRatio = $targetWidth / $targetHeight;
                $srcX = 0;
                $srcY = 0;
                $srcW = $sourceWidth;
                $srcH = $sourceHeight;

                if ($sourceRatio > $targetRatio) {
                    $srcW = $sourceHeight * $targetRatio;
                    $srcX = ($sourceWidth - $srcW) / 2;
                } else {
                    $srcH = $sourceWidth / $targetRatio;
                    $srcY = ($sourceHeight - $srcH) / 2;
                }

                $destImage = imagecreatetruecolor($targetWidth, $targetHeight);

                imagecopyresampled(
                    $destImage,
                    $sourceImage,
                    0,
                    0,
                    (int)$srcX,
                    (int)$srcY,
                    $targetWidth,
                    $targetHeight,
                    (int)$srcW,
                    (int)$srcH
                );

                ob_start();
                imagejpeg($destImage, null, 90);
                $processedImage = ob_get_clean();

                $filename = uniqid() . '.jpg';
                $path = 'img/feedback_photos/' . $filename;
                Storage::disk('public')->put($path, $processedImage);

                $validatedData['photo'] = $path;

                imagedestroy($sourceImage);
                imagedestroy($destImage);
            }

            $validatedData['status'] = 34;

            ServiceFeedback::create($validatedData);

            return response()->json(['message' => 'Thank you for your feedback!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(UpdateFeedbackStatusRequest $request, ServiceFeedback $feedback)
    {
        try {
            $feedback->status = $request->input('status');
            $feedback->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Feedback status updated successfully.',
                'feedback' => $feedback
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(ServiceFeedback $feedback)
    {
        try {
            if ($feedback->photo) {
                Storage::disk('public')->delete($feedback->photo);
            }

            $feedback->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Feedback deleted successfully.'
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function batchDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:service_feedback,id'
        ]);

        $feedbackIds = $validated['ids'];
        $deletedCount = 0;

        DB::beginTransaction();
        try {
            $feedbacks = ServiceFeedback::whereIn('id', $feedbackIds)->get();

            foreach ($feedbacks as $feedback) {
                if ($feedback->photo) {
                    Storage::disk('public')->delete($feedback->photo);
                }
                $feedback->delete();
                $deletedCount++;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Successfully deleted {$deletedCount} feedback record(s).",
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
