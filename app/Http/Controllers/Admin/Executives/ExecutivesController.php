<?php

namespace App\Http\Controllers\Admin\Executives;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExecutivesController extends Controller
{
    public function index() {
        return view('pages.admin.executives.dashboard');
    }
}
