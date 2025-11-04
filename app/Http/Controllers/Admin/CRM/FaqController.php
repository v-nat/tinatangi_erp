<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;

class FaqController extends Controller
{
    public function index()
    {
        return view('pages.admin.crm.faqs');
    }

    public function list()
    {
        $faqs = Faq::orderBy('order', 'asc')->get();
        return response()->json(['data' => $faqs]);
    }

    public function store(StoreFaqRequest $request)
    {
        Faq::create($request->validated());

        return response()->json(['success' => 'FAQ created successfully.']);
    }

    public function edit($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['error' => 'FAQ not found.'], 404);
        }
        return response()->json($faq);
    }

    public function update(UpdateFaqRequest $request, $id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['error' => 'FAQ not found.'], 404);
        }

        $faq->update($request->validated());

        return response()->json(['success' => 'FAQ updated successfully.']);
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['error' => 'FAQ not found.'], 404);
        }

        $faq->delete();
        return response()->json(['success' => 'FAQ deleted successfully.']);
    }
}
