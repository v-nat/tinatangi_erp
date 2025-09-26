<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\Sanitizer;
use App\Helpers\MailSender;
use App\Models\Supplier;
use App\Models\User;

class SupplierController extends Controller
{
    //
    public function getSupplier()
    {
        try {
            $suppliers = Supplier::with(['statusRS'])->orderBy('created_at', 'desc')->get();
            // dd($suppliers);
            return response()->json([
                'data' => $suppliers->map(function ($e) {
                    return [
                        'supplier_id'       => $e->id,
                        'name'              => $e->supplier_name ?? 'N/A',
                        'email'             => $e->email ?? 'N/A',
                        'phone_number'      => $e->phone_number ?? 'N/A',
                        'status'            => $e->statusRS->status ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getActiveSupplier()
    {
        $suppliers = Supplier::where('status', 1)
            ->select('id', 'supplier_name')
            ->get();
        // dd ($positions);

        return response()->json($suppliers);
    }
    public function storeSupplier(StoreSupplierRequest $request)
    {
         try {
            DB::beginTransaction();

            $validated = $request->validated();

            $year = Carbon::now()->format('Y');
            do {
                $random = rand(10000, 99999);
                $supplier_id = $year . $random;
            } while (User::pluck('id')->contains($supplier_id));

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
        $validated = $request->validated();
        
    }
}
