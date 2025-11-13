<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Models\User;
use App\Models\Supplier;
use App\Helpers\Sanitizer;
use App\Helpers\MailSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Controllers\GenerateIdController;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    public function getSupplier()
    {
        try {
            $suppliers = Supplier::with(['statusRS'])->orderBy('created_at', 'desc')->get();

            return response()->json([
                'data' => $suppliers->map(function ($e) {
                    return [
                        'supplier_id'       => $e->id,
                        'name'              => $e->supplier_name ?? 'N/A',
                        'email'             => $e->email ?? 'N/A',
                        'phone_number'      => $e->phone_number ?? 'N/A',
                        'status'            => optional($e->statusRS)->status ?? 'inactive',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getActiveSupplier(Request $request)
    {
        $suppliersQuery = Supplier::where('status', 1)
            ->select('id', 'supplier_name');

        $supplierIds = $request->input('supplier_ids');
        if ($supplierIds) {
            $ids = array_filter(explode(',', $supplierIds), static function ($value) {
                return $value !== null && $value !== '';
            });
            if (!empty($ids)) {
                $suppliersQuery->whereIn('id', $ids);
            }
        }

        $supplierNames = $request->input('supplier_names');
        if ($supplierNames) {
            $names = array_filter(explode(',', $supplierNames), static function ($value) {
                return $value !== null && $value !== '';
            });
            if (!empty($names)) {
                $suppliersQuery->whereIn('supplier_name', $names);
            }
        }

        $suppliers = $suppliersQuery->orderBy('supplier_name')->get();

        return response()->json($suppliers);
    }
    public function storeSupplier(StoreSupplierRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $supplier_id = GenerateIdController::generateID('supplier');

            // Create accounts
            $user = User::create([
                'id' => $supplier_id,
                'first_name' => $validated['supplier_name'],
                'middle_name' => null,
                'last_name' => 'supplier',
                'email' => $validated['email'],
                'password' => bcrypt($supplier_id . '@' . Sanitizer::clean($_POST["supplier_name"])),
                'phone_number' => $validated['phone_number'],
                'user_type' => 'supplier',
                'must_change_password' => true,
            ]);

            $user->save();

            Supplier::create([
                'id' => User::where('email', $validated['email'])->first()->id,
                'user_id' => User::where('email', $validated['email'])->first()->id,
                'supplier_name' => $validated['supplier_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'status' => 1,
            ]);
            DB::commit();
            // Send email with login details
            $content = [
                'email' => $validated['email'],
                'title' => 'Welcome aboard to Tinatangi Cafe!',
                'name' => $validated['supplier_name'],
                'password' => $supplier_id . '@' . Sanitizer::clean($_POST["supplier_name"]),
                'blade_file' => 'emails.new-employee',
                'login_link' => url('/login'),
            ];

            MailSender::sendEmployeeEmail($content);

            return response()->json(['message' => 'Supplier added successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show(Supplier $supplier)
    {
        return response()->json([
            'data' => [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->supplier_name,
                'email' => $supplier->email,
                'phone_number' => $supplier->phone_number,
                'status' => optional($supplier->statusRS)->status,
            ],
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $supplier->update([
                'supplier_name' => $validated['supplier_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
            ]);

            if ($supplier->user) {
                $supplier->user->update([
                    'first_name' => $validated['supplier_name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone_number'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Supplier updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
