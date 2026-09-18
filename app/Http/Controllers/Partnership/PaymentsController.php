<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Models\BurgundyClient;
use App\Services\BurgundyLedger;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index(Request $request, BurgundyLedger $ledger)
    {
        $query = $ledger->payments()->with('subscription');

        if ($type = $request->query('type')) {
            match ($type) {
                'failed'    => $query->where('status', 'failed'),
                'refunded'  => $query->whereIn('type', ['refund', 'void']),
                'initial'   => $query->where('type', 'initial'),
                'recurring' => $query->where('type', 'recurring'),
                default     => null,
            };
        }

        $payments = $query->orderByDesc('charged_at')->paginate(50)->withQueryString();

        // Map subscription → client once, rather than querying per row.
        $clients = BurgundyClient::whereNotNull('subscription_id')
            ->get(['id', 'first_name', 'last_name', 'subscription_id'])
            ->keyBy('subscription_id');

        return view('partnership.payments', [
            'payments' => $payments,
            'clients'  => $clients,
            'type'     => $type,
            'm'        => $ledger->metrics(),
        ]);
    }
}
