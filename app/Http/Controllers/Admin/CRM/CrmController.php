<?php

namespace App\Http\Controllers\Admin\CRM;

use Exception;
use App\Models\Faq;
use App\Models\ServiceFeedback;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Location\Facades\Location;
use App\Http\Requests\StoreServiceFeedbackRequest;
use App\Http\Requests\UpdateFeedbackStatusRequest;

class CrmController extends Controller
{
    public function index()
    {
        return view('pages.admin.crm.index');
    }
    public function serviceFeedback()
    {
        return view('pages.admin.crm.feedback-moderation');
    }

    public function getPublicFaqs()
    {
        $faqs = Faq::where('status', 35) 
                     ->orderBy('order', 'asc')
                     ->get();

        return response()->json(['data' => $faqs]);
    }

    public function fetchPublicTestimonials()
    {
        $testimonials = ServiceFeedback::whereNotNull('message')
            ->where('message', '!=', '')
            ->where('status', 35)
            ->latest()
            ->limit(10)
            ->select('name', 'message', 'photo')
            ->get();

        return response()->json($testimonials);
    }
}
