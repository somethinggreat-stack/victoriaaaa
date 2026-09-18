<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Models\BurgundyClient;
use App\Services\BurgundyLedger;

class DashboardController extends Controller
{
    public function index(BurgundyLedger $ledger)
    {
        $recent = BurgundyClient::with('subscription')
            ->latest('updated_at')
            ->limit(10)
            ->get();

        $needsAttention = BurgundyClient::with('subscription')
            ->where(function ($q) {
                $q->where('match_status', 'needs_review')
                  ->orWhereNotNull('possible_duplicate_of');
            })
            ->limit(5)
            ->get();

        return view('partnership.dashboard', [
            'm'              => $ledger->metrics(),
            'recent'         => $recent,
            'needsAttention' => $needsAttention,
            'checkoutUrl'    => route('burgundy.checkout.show'),
        ]);
    }
}
