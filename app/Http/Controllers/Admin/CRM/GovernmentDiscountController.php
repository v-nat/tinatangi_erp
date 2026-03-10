<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Models\GovernmentDiscountType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GovernmentDiscountController extends Controller
{
    public function index()
    {
        return view('pages.admin.crm.government-discounts');
    }

    public function list()
    {
        $types = GovernmentDiscountType::orderBy('name')->get();
        return response()->json(['data' => $types]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'percentage'  => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:255',
            'is_active'   => 'required|boolean',
        ]);

        GovernmentDiscountType::create($validated);

        return response()->json(['success' => 'Discount type created successfully.']);
    }

    public function edit($id)
    {
        $type = GovernmentDiscountType::find($id);
        if (!$type) {
            return response()->json(['error' => 'Discount type not found.'], 404);
        }
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $type = GovernmentDiscountType::find($id);
        if (!$type) {
            return response()->json(['error' => 'Discount type not found.'], 404);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'percentage'  => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:255',
            'is_active'   => 'required|boolean',
        ]);

        $type->update($validated);

        return response()->json(['success' => 'Discount type updated successfully.']);
    }

    public function destroy($id)
    {
        $type = GovernmentDiscountType::find($id);
        if (!$type) {
            return response()->json(['error' => 'Discount type not found.'], 404);
        }

        $type->delete();

        return response()->json(['success' => 'Discount type deleted successfully.']);
    }

    public function toggleActive($id)
    {
        $type = GovernmentDiscountType::find($id);
        if (!$type) {
            return response()->json(['error' => 'Discount type not found.'], 404);
        }

        $type->is_active = !$type->is_active;
        $type->save();

        return response()->json([
            'success'   => 'Status updated.',
            'is_active' => $type->is_active,
        ]);
    }
}
