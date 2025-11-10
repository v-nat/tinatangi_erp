<?php

namespace App\Http\Controllers\Admin\Inventory;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Product;
use App\Models\ItemUnit;
use Illuminate\Http\Request;
use App\Models\UnitConversion;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        return view('pages.admin.inventory.products');
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('productCategoryRS', 'ingredients');
        $availableServings = $product->syncAvailability();

        $formattedProduct = [
            'id'            => $product->id,
            'name'          => $product->name,
            'description'   => $product->description,
            'base_price'    => (float) $product->base_price,
            'category_name' => optional($product->productCategoryRS)->name ?? 'Uncategorized',
            'status'        => (int) $product->status,
            'status_text'   => Status::getStatusText($product->status),
            'image'         => $product->image,
            'servings'      => $product->servings,
            'available_servings' => $availableServings,
        ];

        return response()->json([
            'product' => $formattedProduct
        ]);
    }

    public function getProductData()
    {
        try {
            $products = Product::with(['productCategoryRS', 'ingredients'])->get();

            return response()->json([
                'data' => $products->map(function ($product) {
                    $availableServings = $product->syncAvailability();

                    return [
                        'id'            => $product->id,
                        'name'          => $product->name,
                        'desc'          => $product->description,
                        'base_price'    => $product->base_price,
                        'category_name' => optional($product->productCategoryRS)->name,
                        'servings'      => $product->servings,
                        'available_servings' => $availableServings,
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

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
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
            imagecopyresampled($destImage, $sourceImage, 0, 0, (int)$srcX, (int)$srcY, $targetWidth, $targetHeight, (int)$srcW, (int)$srcH);
            ob_start();
            imagejpeg($destImage, null, 90);
            $processedImage = ob_get_clean();

            $filename = uniqid() . '.jpg';
            $path = 'img/products/' . $filename;
            Storage::disk('public')->put($path, $processedImage);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validatedData['image'] = $path;

            imagedestroy($sourceImage);
            imagedestroy($destImage);
        }

        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => $product
        ], 200);
    }

    /**
     * Calculate the number of servings a product can make based on ingredient stock.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServings(Product $product): JsonResponse
    {
        $product->load('ingredients');
        $availableServings = $product->syncAvailability();

        return response()->json([
            'servings' => $availableServings,
            'status' => Status::getStatusText($product->status),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product.'
            ], 500);
        }
    }

    public function batchDestroy(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:products,id',
        ]);

        try {
            $productIds = $request->input('ids');

            $products = Product::whereIn('id', $productIds)->get();

            foreach ($products as $product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
            }

            Product::whereIn('id', $productIds)->delete();

            return response()->json([
                'message' => 'Selected products deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
