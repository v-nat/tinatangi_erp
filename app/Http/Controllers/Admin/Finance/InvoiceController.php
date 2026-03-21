<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Status;

class InvoiceController extends Controller
{
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
                'purchaseRequestRS.purchaseOrders.purchaseOrderDetail.itemss.unit',
            ])->where('id', $id)->first();

            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found.'], 404);
            }

            $purchaseOrdersData = collect();

            $pr = $invoice->purchaseRequestRS;

            if ($pr) {
                $pr->purchaseOrders->each(function ($order) use (&$purchaseOrdersData) {
                    $mappedDetails = $order->purchaseOrderDetail->map(function ($detail) {
                        $orderedQty   = (int) $detail->quantity;
                        $deliveredQty = (int) $detail->delivered_qnty;
                        $backorderQty = (int) $detail->backorder_qnty;
                        $unitPrice    = (float) $detail->unit_price;
                        $statusCode   = (int) $detail->status;

                        $displayQty = $deliveredQty > 0 ? $deliveredQty : $orderedQty;
                        $lineTotal  = $displayQty * $unitPrice;

                        $isFullReturn    = $statusCode === 22 && $deliveredQty === 0;
                        $isPartialReturn = $statusCode === 22 && $deliveredQty > 0;

                        return [
                            'item_name'         => optional($detail->itemss)->name,
                            'item_unit'         => optional(optional($detail->itemss)->unit)->abbreviation,
                            'item_unit_name'    => optional(optional($detail->itemss)->unit)->name,
                            'quantity'          => $displayQty,
                            'ordered_qty'       => $orderedQty,
                            'delivered_qty'     => $deliveredQty,
                            'backorder_qty'     => $backorderQty,
                            'unit_price'        => $unitPrice,
                            'total_amount'      => $lineTotal,
                            'status_code'       => $statusCode,
                            'status_text'       => Status::getStatusText($detail->status),
                            'is_returned'       => $isFullReturn,
                            'is_partial_return' => $isPartialReturn,
                        ];
                    });

                    $purchaseOrdersData->push([
                        'purchase_order_id' => $order->purchase_orderId,
                        'created_by_id'     => optional(optional($order->employeeRS)->userRS)->full_name,
                        'details'           => $mappedDetails,
                    ]);
                });
            }

            $calculatedTotal = $purchaseOrdersData->sum(function ($po) {
                return collect($po['details'])->sum('total_amount');
            });

            $finalResponseData = [
                'id'            => $invoice->id,
                'order_id'      => $invoice->order_id,
                'total_amount'  => $calculatedTotal,
                'delivery_no'   => $invoice->delivery_no,
                'date_received' => $invoice->date_received,
                'date_approved' => $invoice->date_approved,

                'overall_photo_path' => $invoice->overall_photo_path,

                'supplier_name' => optional($invoice->supplier)->supplier_name,
                'approved_by_id'    => optional($invoice->userRS)->full_name,

                'purchase_orders' => $purchaseOrdersData->toArray(),
            ];

            return response()->json(['data' => $finalResponseData]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
