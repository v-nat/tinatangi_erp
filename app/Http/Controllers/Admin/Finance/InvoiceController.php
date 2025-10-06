<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    //
    public function getInvoiceForViewing($id)
    {
        try {
            $invoice = Invoice::with([
                'userRS',
                'supplier',
                'purchaseRequestRS',
                'purchaseRequestRS.purchaseOrders.supplierRS',
                'purchaseRequestRS.purchaseOrders',
                'purchaseRequestRS.purchaseOrders.purchaseOrderDetail',
                'purchaseRequestRS.purchaseOrders.purchaseOrderDetail.itemss',
            ])->where('id', $id)->first();

            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found.'], 404);
            }

            $purchaseOrdersData = collect();

            $pr = $invoice->purchaseRequestRS;

            if ($pr) {
                $pr->purchaseOrders->each(function ($order) use (&$purchaseOrdersData) {
                    $mappedDetails = $order->purchaseOrderDetail->map(function ($detail) {
                        return [
                            'item_name'    => optional($detail->itemss)->name,
                            'item_unit'    => optional($detail->itemss)->unit,
                            'quantity'     => (int)$detail->quantity,
                            'unit_price'   => (float)$detail->unit_price,
                            'total_amount' => (float)$detail->total_amount,
                        ];
                    });

                    $purchaseOrdersData->push([
                        'purchase_order_id' => $order->purchase_orderId,
                        'created_by_id'     => optional(optional($order->employeeRS)->userRS)->full_name,
                        'details'           => $mappedDetails,
                    ]);
                });
            }
            // --- Final Response Structure ---
            $finalResponseData = [
                'id'             => $invoice->id,
                'order_id'       => $invoice->order_id,
                'total_amount'   => (float)$invoice->total_amount,
                'delivery_no'    => $invoice->delivery_no,
                'date_received'  => $invoice->date_received,
                'date_approved'  => $invoice->date_approved,

                'supplier_name'  => optional($invoice->supplier)->supplier_name,
                'approved_by_id'    => optional($invoice->userRS)->full_name,

                'purchase_orders' => $purchaseOrdersData->toArray(),
            ];

            return response()->json(['data' => $finalResponseData]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
