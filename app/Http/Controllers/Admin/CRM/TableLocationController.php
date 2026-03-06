<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\TableLocation;
use Illuminate\Http\Request;

class TableLocationController extends Controller
{
    public function list()
    {
        return response()->json(['data' => TableLocation::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:table_locations,name',
        ]);

        $location = TableLocation::create($data);

        return response()->json(['success' => true, 'location' => $location]);
    }

    public function edit(TableLocation $location)
    {
        return response()->json($location);
    }

    public function update(Request $request, TableLocation $location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:table_locations,name,' . $location->id,
        ]);

        $location->update($data);

        return response()->json(['success' => true, 'location' => $location]);
    }

    public function destroy(TableLocation $location)
    {
        if ($location->tables()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this location — it is assigned to one or more tables.',
            ], 422);
        }

        $location->delete();

        return response()->json(['success' => true]);
    }
}
