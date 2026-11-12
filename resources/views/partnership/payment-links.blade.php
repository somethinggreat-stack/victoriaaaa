@extends('partnership.layout')
@section('title', 'Payment Links')
@section('subtitle', 'Create a one-time payment link for a client and send it to them directly.')

@section('content')

@if($needsSetup)
  <div class="card"><h2>Setting up</h2><p class="sub" style="margin:0">Payment links aren't switched on yet. Please contact support.</p></div>
@else

@if(session('generated_link'))
  <div class="card" style="border-color:var(--green)">
    <h2 style="color:var(--green)">✓ Link ready for {{ session('generated_name') }} · {{ session('generated_amount') }}</h2>
    <p class="sub">Copy it and send it to your client. They pay, then sign the service agreement.</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <input type="text" id="freshLink" readonly value="{{ session('generated_link') }}" onclick="this.select()" class="mono" style="flex:1;min-width:240px">
      <button type="button" class="btn" data-copy="{{ session('generated_link') }}">Copy link</button>
    </div>
  </div>
@endif

<div class="tiles">
  <div class="tile"><div class="k">Links</div><div class="v">{{ $kpis['total'] }}</div><div class="n">{{ $kpis['unpaid'] }} unpaid</div></div>
  <div class="tile green"><div class="k">Paid</div><div class="v">{{ $kpis['paid'] }}</div><div class="n">links paid</div></div>
  <div class="tile green"><div class="k">Collected</div><div class="v">${{ number_format($kpis['collected'], 0) }}</div><div class="n">one-time payments</div></div>
  <div class="tile"><div class="k">Outstanding</div><div class="v">${{ number_format($kpis['outstanding'], 0) }}</div><div class="n">not yet paid</div></div>
</div>

<div class="card">
  <h2>Create a payment link</h2>
  <p class="sub">One-time charge for any amount. {{ $prefill ? 'Filled in for ' . $prefill->full_name . '.' : '' }}</p>
  <form method="POST" action="{{ route('partnership.payment-links.store') }}">
    @csrf
    @if($prefill)<input type="hidden" name="burgundy_client_id" value="{{ $prefill->id }}">@endif

    <div class="field">
      <label class="fl">Client name *</label>
      <input type="text" name="client_name" maxlength="150" required value="{{ old('client_name', $prefill?->full_name) }}" placeholder="Jane Smith">
      @error('client_name')<div class="mut" style="color:var(--red)">{{ $message }}</div>@enderror
    </div>
    <div class="field">
      <label class="fl">Amount (one-time) *</label>
      <input type="number" name="amount" step="0.01" min="1" max="100000" required value="{{ old('amount', '450.00') }}">
      @error('amount')<div class="mut" style="color:var(--red)">{{ $message }}</div>@enderror
    </div>
    <div class="field">
      <label class="fl">Client email (optional)</label>
      <input type="email" name="email" maxlength="150" value="{{ old('email', $prefill?->email) }}" placeholder="client@email.com">
      @error('email')<div class="mut" style="color:var(--red)">{{ $message }}</div>@enderror
    </div>
    <div class="field">
      <label class="fl">Note on payment page (optional)</label>
      <input type="text" name="note" maxlength="255" value="{{ old('note') }}" placeholder="e.g. Credit repair — one-time fee">
    </div>
    <div class="field">
      <label class="fl">Service description *</label>
      <textarea name="service_description" rows="2" maxlength="500" required placeholder="e.g. One-time credit repair fee: 3-bureau audit and dispute rounds.">{{ old('service_description', 'One-time credit repair fee') }}</textarea>
      <div class="mut" style="font-size:11.5px;margin-top:4px">Appears on the service agreement the client signs right after paying.</div>
      @error('service_description')<div class="mut" style="color:var(--red)">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn">Generate link</button>
  </form>
</div>

<div class="card">
  @if($rows->isEmpty())
    <div class="empty"><strong>No payment links yet</strong>Create one above and send it to your client.</div>
  @else
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>Client</th><th>Amount</th><th>Status</th><th>Link</th><th>Paid</th><th>Created</th><th></th></tr></thead>
        <tbody>
        @foreach($rows as $r)
          <tr>
            <td>
              @if($r->burgundy_client_id)
                <a class="nm" href="{{ route('partnership.clients.show', $r->burgundy_client_id) }}">{{ $r->client_name }}</a>
              @else
                <span class="nm">{{ $r->client_name }}</span>
              @endif
              <div class="mut" style="font-size:11.5px">{{ $r->email ?: '—' }}</div>
            </td>
            <td style="font-weight:700">${{ number_format((float) $r->amount, 2) }}</td>
            <td>@php $tone = ['unpaid' => 'amber', 'paid' => 'green', 'void' => 'grey'][$r->status] ?? 'grey'; @endphp
              <span class="pill {{ $tone }}">{{ ucfirst($r->status) }}</span></td>
            <td>
              @if($r->status === 'unpaid')
                <button type="button" class="btn ghost sm" data-copy="{{ $r->url }}">Copy link</button>
              @else — @endif
            </td>
            <td class="mut">{{ $r->status === 'paid' ? ($r->paid_at?->format('M j, Y') ?: '—') : '—' }}</td>
            <td class="mut">{{ $r->created_at->format('M j, Y') }}</td>
            <td>
              @if($r->status === 'unpaid')
                <form method="POST" action="{{ route('partnership.payment-links.void', $r) }}" onsubmit="return confirm('Void this link so it can no longer be paid?');">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn ghost sm">Void</button>
                </form>
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="pager">
      <div>Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }}</div>
      <div class="links">{!! $rows->links('vendor.pagination.admin') !!}</div>
    </div>
  @endif
</div>

@push('scripts')
<script>
document.addEventListener('click', function (e) {
  var btn = e.target.closest('[data-copy]');
  if (!btn) return;
  var text = btn.getAttribute('data-copy'), old = btn.textContent;
  function done() { btn.textContent = '✓ Copied'; setTimeout(function () { btn.textContent = old; }, 1800); }
  if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done); return; }
  var t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select();
  try { document.execCommand('copy'); done(); } catch (_) {} document.body.removeChild(t);
});
</script>
@endpush

@endif
@endsection
