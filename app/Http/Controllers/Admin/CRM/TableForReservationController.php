<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\TableForReservation;
use App\Http\Requests\StoreTableForReservationRequest;
use App\Http\Requests\UpdateTableForReservationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Exception;

class TableForReservationController extends Controller
{
    public function list(): JsonResponse
    {
        $tables = TableForReservation::all();
        return response()->json(['data' => $tables]);
    }

    public function store(StoreTableForReservationRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $validated['image'] = $this->processAndStoreImage($request->file('image'));
            }

            $validated['status'] = $validated['status'] ?? 1;

            $table = TableForReservation::create($validated);

            return response()->json(['success' => 'Table created successfully.', 'table' => $table], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit(TableForReservation $table): JsonResponse
    {
        return response()->json($table);
    }

    public function update(UpdateTableForReservationRequest $request, TableForReservation $table): JsonResponse
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                if ($table->image) {
                    $oldPath = str_replace('/storage', 'public', $table->image);
                    Storage::delete($oldPath);
                }

                $validated['image'] = $this->processAndStoreImage($request->file('image'));
            }

            $table->update($validated);

            return response()->json(['success' => 'Table updated successfully.', 'table' => $table]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(TableForReservation $table): JsonResponse
    {
        try {
            if ($table->image) {
                $oldPath = str_replace('/storage', 'public', $table->image);
                Storage::delete($oldPath);
            }

            $table->delete();
            return response()->json(['success' => 'Table deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete table.'], 500);
        }
    }

    /**
     * Process, crop, and store the uploaded image.
     *
     * @param \Illuminate\Http\UploadedFile $imageFile
     * @return string The public URL of the stored image.
     * @throws \Exception
     */
    private function processAndStoreImage($imageFile): string
    {
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
                throw new Exception('Unsupported image type.');
        }

        $targetWidth = 350;
        $targetHeight = 350;

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
        $storagePath = 'public/img/table_images/' . $filename;
        Storage::put($storagePath, $processedImage);

        imagedestroy($sourceImage);
        imagedestroy($destImage);

        return $storagePath;
    }
}
