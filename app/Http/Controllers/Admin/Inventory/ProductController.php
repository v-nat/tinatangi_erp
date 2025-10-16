<?php

namespace App\Http\Controllers\Admin\Inventory;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        return view('pages.admin.inventory.products');
    }

    public function getProductData()
    {
        try {
            $products = Product::with(['productCategoryRS'])->orderBy('name')->get();

            return response()->json([
                'data' => $products->map(function ($product) {
                    return [
                        'id'            => $product->id,
                        'name'          => $product->name,
                        'base_price'    => $product->base_price,
                        'category_name' => optional($product->productCategoryRS)->name,
                        'status'        => Status::getStatusText($product->status),
                        'created_at'    => Carbon::parse($product->created_at)->format('M d, Y'),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch products.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategories()
    {
        $categories = ProductCategory::whereNot('name', 'All')->orderBy('name')->get();
        return response()->json($categories);
    }

    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $sourcePath = $imageFile->getRealPath();

            // 1. Get original image dimensions and type
            list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

            // 2. Create an image resource from the uploaded file based on its type
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
                    // Handle unsupported file type
                    return response()->json(['message' => 'Unsupported image type.'], 422);
            }

            // 3. Define target dimensions for the square crop
            $targetWidth = 250;
            $targetHeight = 250;

            // 4. Calculate the source coordinates for a center crop
            $sourceRatio = $sourceWidth / $sourceHeight;
            $targetRatio = $targetWidth / $targetHeight;
            $srcX = 0;
            $srcY = 0;
            $srcW = $sourceWidth;
            $srcH = $sourceHeight;

            if ($sourceRatio > $targetRatio) { // Image is wider than target
                $srcW = $sourceHeight * $targetRatio;
                $srcX = ($sourceWidth - $srcW) / 2;
            } else { // Image is taller than or same ratio as target
                $srcH = $sourceWidth / $targetRatio;
                $srcY = ($sourceHeight - $srcH) / 2;
            }

            // 5. Create a new, blank destination image canvas
            $destImage = imagecreatetruecolor($targetWidth, $targetHeight);

            // 6. Copy and resize the cropped portion of the source image to the destination
            imagecopyresampled(
                $destImage,
                $sourceImage,
                0,
                0, // Destination X, Y
                (int)$srcX,
                (int)$srcY, // Source X, Y
                $targetWidth,
                $targetHeight, // Destination Width, Height
                (int)$srcW,
                (int)$srcH  // Source Width, Height
            );

            // 7. Capture the processed image output into a variable
            ob_start();
            imagejpeg($destImage, null, 90); // Output as JPEG with 90% quality
            $processedImage = ob_get_clean();

            // 8. Save the processed image using Laravel's Storage
            $filename = uniqid() . '.jpg';
            $path = 'img/products/' . $filename;
            Storage::disk('public')->put($path, $processedImage);

            $validatedData['image'] = $path;

            // 9. Free up memory
            imagedestroy($sourceImage);
            imagedestroy($destImage);
        }

        $product = Product::create($validatedData);

        return response()->json([
            'message' => 'Product added successfully!',
            'product' => $product
        ], 201);
    }
}
