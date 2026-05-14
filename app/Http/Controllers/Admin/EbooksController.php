<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\EbookOrder;
use Illuminate\Http\Request;

class EbooksController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // Catalog
    // ════════════════════════════════════════════════════════════════
    public function index()
    {
        $ebooks = Ebook::orderBy('sort_order')->orderBy('title')->get();

        // Aggregate per-ebook sales for the cards
        $sales = EbookOrder::query()
            ->selectRaw('ebook_slug, COUNT(*) as orders, SUM(amount) as revenue')
            ->where('status', 'paid')
            ->groupBy('ebook_slug')
            ->get()
            ->keyBy('ebook_slug');

        return view('admin.ebooks.index', [
            'ebooks' => $ebooks,
            'sales'  => $sales,
        ]);
    }

    public function edit(Ebook $ebook)
    {
        return view('admin.ebooks.edit', [
            'ebook' => $ebook,
        ]);
    }

    public function update(Ebook $ebook, Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:200',
            'subtitle'   => 'nullable|string|max:255',
            'price'      => 'required|numeric|min:0|max:9999.99',
            'drive_link' => 'nullable|url|max:2048',
            'is_active'  => 'nullable|boolean',
            'features'   => 'nullable|string',
        ]);

        $features = null;
        if (!empty($validated['features'])) {
            $features = array_values(array_filter(array_map(
                'trim',
                preg_split("/\r\n|\n|\r/", $validated['features'])
            )));
            $features = array_slice($features, 0, 12);
        }

        $ebook->update([
            'title'      => $validated['title'],
            'subtitle'   => $validated['subtitle'] ?? null,
            'price'      => $validated['price'],
            'drive_link' => $validated['drive_link'] ?? null,
            'is_active'  => (bool) ($request->input('is_active') === '1' || $request->input('is_active') === 1),
            'features'   => $features,
        ]);

        return redirect()->route('admin.ebooks')->with('success', 'eBook "' . $ebook->title . '" updated.');
    }

    // ════════════════════════════════════════════════════════════════
    // Orders
    // ════════════════════════════════════════════════════════════════
    public function orders(Request $request)
    {
        $q = EbookOrder::query()->with('ebook');

        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('email',          'like', "%{$search}%")
                  ->orWhere('first_name',    'like', "%{$search}%")
                  ->orWhere('last_name',     'like', "%{$search}%")
                  ->orWhere('phone',         'like', "%{$search}%")
                  ->orWhere('invoice_number','like', "%{$search}%")
                  ->orWhere('transaction_id','like', "%{$search}%")
                  ->orWhere('ebook_title',   'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        if ($slug = $request->query('ebook')) {
            $q->where('ebook_slug', $slug);
        }

        $kpis = [
            'total_orders'  => EbookOrder::count(),
            'paid_lifetime' => (float) EbookOrder::where('status', 'paid')->sum('amount'),
            'today_count'   => EbookOrder::whereDate('charged_at', today())->count(),
            'today_amount'  => (float) EbookOrder::whereDate('charged_at', today())
                                ->where('status', 'paid')
                                ->sum('amount'),
            'mtd_amount'    => (float) EbookOrder::whereMonth('charged_at', now()->month)
                                ->whereYear('charged_at', now()->year)
                                ->where('status', 'paid')
                                ->sum('amount'),
        ];

        $ebooks = Ebook::orderBy('title')->get(['slug', 'title']);

        return view('admin.ebooks.orders', [
            'rows'   => $q->latest('charged_at')->paginate(50)->withQueryString(),
            'kpis'   => $kpis,
            'ebooks' => $ebooks,
        ]);
    }

    public function orderShow(EbookOrder $order)
    {
        $order->load('ebook');

        return view('admin.ebooks.order-show', [
            'order' => $order,
        ]);
    }
}
