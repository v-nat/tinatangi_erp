<?php

namespace App\Http\Controllers\Admin\CRM;

use Carbon\Carbon;
use App\Models\Announcement;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('pages.admin.crm.announcements');
    }

    public function list()
    {
        $announcements = Announcement::orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $announcements]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'           => 'required|in:announcement,discount',
            'title'          => 'required|string|max:255',
            'content'        => 'nullable|string',
            'badge_text'     => 'nullable|string|max:50',
            'icon'           => 'nullable|string|max:100',
            'theme'          => 'required|in:gold,crimson,forest,ocean,royal,slate',
            'bg_style'       => 'required|in:solid,gradient,glow',
            'discount_type'  => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'min_spend'      => 'nullable|numeric|min:0',
            'applicable_to'  => 'required_if:type,discount|in:all,specific',
            'usage_limit'    => 'nullable|integer|min:1',
            'product_ids'    => 'required_if:applicable_to,specific|array',
            'product_ids.*'  => 'integer|exists:products,id',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'display_order'  => 'nullable|integer|min:0',
            'status'         => 'required|in:34,35',
        ]);

        // Never set usage_count from form
        unset($validated['product_ids']);

        $announcement = Announcement::create($validated);
        $announcement->products()->sync($request->input('product_ids', []));

        return response()->json(['success' => 'Announcement created successfully.']);
    }

    public function edit($id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json(['error' => 'Announcement not found.'], 404);
        }

        $data = $announcement->toArray();
        $data['product_ids'] = $announcement->products()->pluck('product_id');

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json(['error' => 'Announcement not found.'], 404);
        }

        $validated = $request->validate([
            'type'           => 'required|in:announcement,discount',
            'title'          => 'required|string|max:255',
            'content'        => 'nullable|string',
            'badge_text'     => 'nullable|string|max:50',
            'icon'           => 'nullable|string|max:100',
            'theme'          => 'required|in:gold,crimson,forest,ocean,royal,slate',
            'bg_style'       => 'required|in:solid,gradient,glow',
            'discount_type'  => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'min_spend'      => 'nullable|numeric|min:0',
            'applicable_to'  => 'required_if:type,discount|in:all,specific',
            'usage_limit'    => 'nullable|integer|min:1',
            'product_ids'    => 'required_if:applicable_to,specific|array',
            'product_ids.*'  => 'integer|exists:products,id',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'display_order'  => 'nullable|integer|min:0',
            'status'         => 'required|in:34,35',
        ]);

        unset($validated['product_ids']);

        $announcement->update($validated);
        $announcement->products()->sync($request->input('product_ids', []));

        return response()->json(['success' => 'Announcement updated successfully.']);
    }

    public function destroy($id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json(['error' => 'Announcement not found.'], 404);
        }

        $announcement->delete();

        return response()->json(['success' => 'Announcement deleted successfully.']);
    }

    public function batchDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:announcements,id',
        ]);

        Announcement::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => 'Selected announcements deleted successfully.']);
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return response()->json(['error' => 'Announcement not found.'], 404);
        }

        $announcement->status = $announcement->status === 35 ? 34 : 35;
        $announcement->save();

        return response()->json([
            'success' => 'Status updated.',
            'status'  => $announcement->status,
        ]);
    }

    public function getAvailableProducts()
    {
        $products = Product::whereNull('deleted_at')
            ->where('status', 1)
            ->with(['productCategoryRS:id,name', 'ingredients'])
            ->select('id', 'name', 'base_price', 'product_category_id')
            ->get()
            ->map(function ($product) {
                return [
                    'id'                 => $product->id,
                    'name'               => $product->name,
                    'base_price'         => (float) $product->base_price,
                    'category_name'      => optional($product->productCategoryRS)->name,
                    'available_servings' => $product->calculateAvailableServings(),
                ];
            })
            ->filter(fn ($p) => $p['available_servings'] > 0)
            ->values();

        return response()->json(['data' => $products]);
    }

    public function fetchPublicAnnouncements()
    {
        $today = Carbon::today()->toDateString();

        $announcements = Announcement::where('status', 35)
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $today);
            })
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json(['data' => $announcements]);
    }
}
