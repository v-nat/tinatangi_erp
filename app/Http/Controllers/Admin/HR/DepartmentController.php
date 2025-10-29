<?php

namespace App\Http\Controllers\Admin\HR;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function getDepartmentList()
    {
        $departments = Department::where('deleted_at', null)
        ->whereNot('name', 'Administrator')
        ->orderBy('name')->get(['id', 'name']);
        return response()->json(['data' => $departments]);
    }
}
