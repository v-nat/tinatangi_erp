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

            $targetWidth = 250;
            $targetHeight = 250;

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
            $path = 'img/products/' . $filename;
            Storage::disk('public')->put($path, $processedImage);

            $validatedData['image'] = $path;

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
