<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\BudgetRelease;
use App\Models\InventoryItem;
use App\Models\PurchaseRequest;
use App\Http\Controllers\Controller;

class GenerateIdController extends Controller
{
    //
    public static function generateID($type)
    {
        $_id = "";
        $year = Carbon::now()->format('Y');

        switch ($type) {
            case 'invoice':
                do {
                    $random = rand(10000, 99999);
                    $_id = $year . $random;
                } while (PurchaseRequest::pluck('invoice_id')->contains($_id));
                break;
            case 'order':
                $prefix = 'ORD_';
                $key = 'order_id';

                do {
                    $_id = $prefix . rand(100000, 999999);
                } while (Order::where($key, $_id)->exists());
                break;

            case 'delivery_no':
                do {
                    $random = rand(10000, 99999);
                    $_id = $year . $random;
                } while (Invoice::pluck('delivery_no')->contains($_id));
                break;

            case 'purchase_order':
                do {
                    $random = rand(10000, 99999);
                    $_id = $year . $random;
                } while (PurchaseRequest::pluck('id')->contains($_id));

                return response()->json(['order_id' => $_id]);

            case 'purchase_request':
                do {
                    $random = rand(10000, 99999);
                    $_id = $year . $random;
                } while (PurchaseRequest::pluck('id')->contains($_id));
                break;

            case 'sku':
                do {
                    $random = rand(10000, 99999);
                    $_id = 'SKU-' . $year . $random;
                } while (InventoryItem::pluck('sku')->contains($_id));
                break;

            case 'employee':
            case 'supplier':
                do {
                    $random = rand(10000, 99999);
                    $_id = $year . $random;
                } while (User::pluck('id')->contains($_id));
                break;

            case 'release_id':
                do {
                    $random = rand(10000, 99999);
                    $_id = $year . $random;
                } while (BudgetRelease::pluck('id')->contains($_id));
                break;

            default:
                throw new \InvalidArgumentException("Invalid ID type provided: {$type}");
        }

        return $_id;
    }
}
