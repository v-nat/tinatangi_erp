<?php

namespace App\Http\Controllers\Admin\Inventory;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Product;
use App\Models\ItemUnit;
use App\Models\UnitConversion;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
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
                        'desc'          => $product->description,
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

    public function getServings(Product $product): JsonResponse
    {
        $product->load('ingredients.item.unitRS');

        if ($product->ingredients->isEmpty()) {
            return response()->json(['servings' => 'N/A']);
        }

        $minServings = PHP_INT_MAX;

        foreach ($product->ingredients as $ingredient) {
            // Ensure we have all the necessary related data to proceed.
            if (!$ingredient->item || !$ingredient->item->unitRS) {
                $minServings = 0;
                break; // Break loop if ingredient data is misconfigured.
            }

            $stockLevel = $ingredient->stock_level;
            $purchaseUnit = $ingredient->item->unitRS;
            $quantityUsedInRecipe = $ingredient->pivot->quantity_used;

            // An ingredient must be used in the recipe to affect the serving count.
            if ($quantityUsedInRecipe <= 0) {
                continue;
            }

            // Find the base unit for the ingredient's measurement type (e.g., 'Gram' for 'weight').
            $baseUnit = ItemUnit::where('type', $purchaseUnit->type)
                ->where('is_base_unit', true)
                ->first();

            if (!$baseUnit) {
                // If there's no base unit defined for this type, calculation is impossible.
                $minServings = 0;
                break;
            }

            $totalStockInBaseUnit = 0;

            // If the stock's unit is already the base unit, no conversion is needed.
            if ($purchaseUnit->id === $baseUnit->id) {
                $totalStockInBaseUnit = $stockLevel;
            } else {
                // Otherwise, find the conversion factor to the base unit.
                $conversion = UnitConversion::where('from_unit_id', $purchaseUnit->id)
                    ->where('to_unit_id', $baseUnit->id)
                    ->first();

                if (!$conversion) {
                    // If no conversion path is defined, we can't make any servings.
                    $minServings = 0;
                    break;
                }

                // **Switched computation logic per unit type for better accuracy and future extension.**
                switch ($purchaseUnit->type) {
                    case 'weight':
                        // Formula: Total Grams = Number of Kilograms * 1000
                        $totalStockInBaseUnit = $stockLevel * $conversion->factor;
                        break;

                    case 'volume':
                        // Formula: Total Milliliters = Number of Liters * 1000
                        $totalStockInBaseUnit = $stockLevel * $conversion->factor;
                        break;

                    case 'count':
                        // Formula: Total Pieces = Number of Boxes * (Pieces per Box)
                        $totalStockInBaseUnit = $stockLevel * $conversion->factor;
                        break;

                    default:
                        // If an unsupported unit type is found, we cannot proceed.
                        $minServings = 0;
                        break 2; // This breaks out of both the switch and the foreach loop.
                }
            }

            // Calculate how many full servings this ingredient's stock can provide.
            $servingsForThisIngredient = floor($totalStockInBaseUnit / $quantityUsedInRecipe);

            // The final serving count is limited by the ingredient that runs out first.
            $minServings = min($minServings, $servingsForThisIngredient);
        }

        // If the loop never ran or was broken, $minServings could still be at its max value.
        $servings = ($minServings === PHP_INT_MAX) ? 0 : $minServings;

        // Automatically update the product's availability status.
        if ($servings <= 0 && $product->status != 2) {
            $product->status = 2; // Set to 'Unavailable'
            $product->save();
        } elseif ($servings > 0 && $product->status != 1) {
            $product->status = 1; // Set to 'Available'
            $product->save();
        }

        return response()->json(['servings' => $servings]);
    }

    // public function getServings(Product $product)
    // {
    //     $product->load('ingredients.item.unitRS');

    //     if ($product->ingredients->isEmpty()) {
    //         $product->status = 2;
    //         $product->save();
    //         return response()->json(['servings' => 'N/A']);
    //     }

    //     $minServings = PHP_INT_MAX;

    //     foreach ($product->ingredients as $ingredient) {
    //         $stockLevel = $ingredient->stock_level;
    //         $purchaseUnit = $ingredient->item->unitRS;
    //         $quantityUsedInRecipe = $ingredient->pivot->quantity_used;

    //         $baseUnit = ItemUnit::where('type', $purchaseUnit->type)
    //             ->where('is_base_unit', true)
    //             ->first();

    //         if (!$baseUnit) {
    //             continue;
    //         }

    //         $totalStockInBaseUnit = $stockLevel;

    //         if ($purchaseUnit->id !== $baseUnit->id) {
    //             $conversion = UnitConversion::where('from_unit_id', $purchaseUnit->id)
    //                 ->where('to_unit_id', $baseUnit->id)
    //                 ->first();

    //             if ($conversion) {
    //                 $totalStockInBaseUnit = $stockLevel * $conversion->factor;
    //             } else {
    //                 $minServings = 0;
    //                 continue;
    //             }
    //         }

    //         if ($quantityUsedInRecipe > 0) {
    //             $servingsForThisIngredient = floor($totalStockInBaseUnit / $quantityUsedInRecipe);
    //             $minServings = min($minServings, $servingsForThisIngredient);
    //         }
    //     }

    //     $servings = ($minServings == PHP_INT_MAX) ? 0 : $minServings;

    //     return response()->json(['servings' => $servings / 1000]);
    // }
}
