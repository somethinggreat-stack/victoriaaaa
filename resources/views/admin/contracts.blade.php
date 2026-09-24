@extends('admin.layout')
@section('title', 'Contracts')

@section('content')
<div class="admin-header">
  <div>
    <h1>Contracts</h1>
    <div class="sub">{{ $rows->total() }} total · service agreements signed after payment</div>
  </div>
  <button type="button" class="adm-btn" onclick="document.getElementById('newAg').style.display='block';window.scrollTo(0,0);">+ Send an agreement</button>
</div>

{{-- For a client who paid somewhere this site did not process — another funnel,
     an invoice, a transfer — or who paid before contracts existed. --}}
<div class="adm-card" id="newAg" style="display:{{ $errors->any() ? 'block' : 'none' }}; margin-bottom:16px;">
  <div class="adm-card-head"><h2>Send an agreement to a client who already paid</h2></div>
  <p style="font-size:13.5px;color:var(--ink-2);margin-bottom:16px;">
    Use this when the payment did not go through this website. You get a signing link to send them —
    the contract will show exactly the amounts you enter here, so put in what they were actually charged.
  </p>

  @if ($errors->any())
    <div class="flash error" style="margin-bottom:14px;">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.contracts.store') }}">
    @csrf
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
      <div>
        <label class="plm-label">Client name <span style="color:#e63179">*</span></label>
        <input class="plm-input" type="text" name="client_name" maxlength="150" required
               placeholder="Brice Wilson" value="{{ old('client_name') }}">
      </div>
      <div>
        <label class="plm-label">Email</label>
        <input class="plm-input" type="email" name="email" maxlength="150"
               placeholder="client@email.com" value="{{ old('email') }}">
        <div style="font-size:11.5px;color:var(--ink-3);margin-top:5px;">Lets you email the link in one click.</div>
      </div>
      <div>
        <label class="plm-label">Phone</label>
        <input class="plm-input" type="text" name="client_phone" maxlength="30"
               placeholder="(469) 555-0134" value="{{ old('client_phone') }}">
      </div>
      <div>
        <label class="plm-label">Whose client <span style="color:#e63179">*</span></label>
        <select class="plm-input" name="partner">
          <option value="victoria" @selected(old('partner','victoria')==='victoria')>Victoria</option>
          <option value="burgundy" @selected(old('partner')==='burgundy')>Burgundy</option>
        </select>
      </div>
      <div>
        <label class="plm-label">Amount they paid <span style="color:#e63179">*</span></label>
        <input class="plm-input" type="number" name="charged_today" step="0.01" min="0" max="100000" required
               placeholder="1200.00" value="{{ old('charged_today') }}">
      </div>
      <div>
        <label class="plm-label">Then, each time</label>
        <input class="plm-input" type="number" name="recurring_amount" step="0.01" min="0" max="100000"
               placeholder="leave blank if one-time" value="{{ old('recurring_amount') }}">
      </div>
      <div>
        <label class="plm-label">How often</label>
        <select class="plm-input" name="recurring_interval">
          <option value="month" @selected(old('recurring_interval','month')==='month')>Every month</option>
          <option value="week"  @selected(old('recurring_interval')==='week')>Every week</option>
        </select>
      </div>
      <div>
        <label class="plm-label">How many times</label>
        <input class="plm-input" type="number" name="recurring_count" min="1" max="120"
               placeholder="blank = until they cancel" value="{{ old('recurring_count') }}">
        <div style="font-size:11.5px;color:var(--ink-3);margin-top:5px;">e.g. 4 for "$250/week x 4 weeks".</div>
      </div>
    </div>

    <div style="margin-top:14px;padding:14px;border:1px solid var(--line);border-radius:10px;background:var(--bg-2);">
      <label style="display:flex;gap:10px;align-items:flex-start;font-size:13.5px;cursor:pointer;">
        <input type="checkbox" name="requires_cosigner" value="1" style="margin-top:3px;width:18px;height:18px;"
               @checked(old('requires_cosigner')) onchange="document.getElementById('coFields').style.display=this.checked?'grid':'none'">
        <span>
          <strong>Two people are on this plan</strong> — both must sign the same agreement.
          <span style="display:block;color:var(--ink-3);font-size:12px;margin-top:3px;">
            One document, one price covering both. Not two separate contracts.
          </span>
        </span>
      </label>

      <div id="coFields" style="display:{{ old('requires_cosigner') ? 'grid' : 'none' }};grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:14px;">
        <div>
          <label class="plm-label">Second person's name <span style="color:#e63179">*</span></label>
          <input class="plm-input" type="text" name="cosigner_name" maxlength="150"
                 placeholder="Brice Wilson" value="{{ old('cosigner_name') }}">
        </div>
        <div>
          <label class="plm-label">Second person's email</label>
          <input class="plm-input" type="email" name="cosigner_email" maxlength="150"
                 placeholder="brice@email.com" value="{{ old('cosigner_email') }}">
        </div>
      </div>
    </div>

    <div style="margin-top:14px;">
      <label class="plm-label">What they are paying for <span style="color:#e63179">*</span></label>
      <textarea class="plm-input" name="service_description" rows="2" maxlength="500" required
                placeholder="e.g. Full-service credit restoration: 3-bureau audit, dispute rounds and ongoing guidance.">{{ old('service_description') }}</textarea>
      <div style="font-size:11.5px;color:var(--ink-3);margin-top:5px;">This appears on the contract they sign.</div>
    </div>

    <div style="margin-top:16px;display:flex;gap:10px;">
      <button class="adm-btn" type="submit">Create agreement &amp; get link</button>
      <button class="adm-btn ghost" type="button" onclick="document.getElementById('newAg').style.display='none'">Cancel</button>
    </div>
  </form>
</div>

@if ($pendingCount > 0 && request('status') !== 'pending')
  <div class="adm-card" style="margin-bottom:14px; border-color:#ffd8a8; background:#fffaf2;">
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
      <div style="flex:1; min-width:260px;">
        <strong style="color:#9a5b00;">{{ $pendingCount }} client{{ $pendingCount === 1 ? '' : 's' }} paid but never signed.</strong>
        <div style="font-size:13px; color:var(--ink-2); margin-top:3px;">
          Their card was charged and the agreement was opened, but they closed the page before signing.
          Open each one and send them the link again.
        </div>
      </div>
      <a class="adm-btn" href="{{ route('admin.contracts', ['status' => 'pending']) }}">Show them</a>
    </div>
  </div>
@endif

<div class="adm-kpis" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px;">
  <div class="adm-card" style="padding:16px 18px;"><div class="sub">Signed</div><div style="font-size:24px;font-weight:700;color:#157a3d;">{{ $signedCount }}</div></div>
  <div class="adm-card" style="padding:16px 18px;"><div class="sub">Awaiting signature</div><div style="font-size:24px;font-weight:700;color:{{ $pendingCount > 0 ? '#9a5b00' : 'inherit' }};">{{ $pendingCount }}</div></div>
</div>

<div class="adm-toolbar">
  <form method="GET" action="{{ route('admin.contracts') }}">
    <input class="adm-input" type="search" name="q" placeholder="Search name, email, invoice or plan" value="{{ request('q') }}">
    <select class="adm-select" name="status">
      <option value="">Any status</option>
      <option value="pending" @selected(request('status')==='pending')>Awaiting signature</option>
      <option value="signed"  @selected(request('status')==='signed')>Signed</option>
    </select>
    <select class="adm-select" name="partner">
      <option value="">Both</option>
      <option value="victoria" @selected(request('partner')==='victoria')>Victoria</option>
      <option value="burgundy" @selected(request('partner')==='burgundy')>Burgundy</option>
    </select>
    <button class="adm-btn" type="submit">Search</button>
  </form>
</div>

@if ($rows->isEmpty())
  <div class="empty"><strong>No contracts match.</strong>Agreements appear here as clients sign them.</div>
@else
  <div class="adm-table-wrap"><table class="adm-table">
    <thead>
      <tr>
        <th>Client</th><th>Service</th><th class="nw">Price</th><th>Status</th>
        <th class="nw">Signed</th><th>Source</th><th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $c)
        <tr>
          <td>
            <a href="{{ route('admin.contracts.show', $c) }}" class="nm">{{ $c->signerName() }}</a>
            <span class="sub">{{ $c->email ?: '—' }}</span>
          </td>
          <td>
            {{ $c->plan_label }}
            @if ($c->partner === 'burgundy')<span class="badge" style="background:#fbeef2;color:#7b1e3c;margin-left:6px;">Burgundy</span>@endif
          </td>
          <td class="nw">{{ $c->priceSummary() }}</td>
          <td>
            @if ($c->isSigned())
              <span class="badge sent">signed</span>
            @elseif ($c->isPartiallySigned())
              <span class="badge pending">1 of 2 signed</span>
            @else
              <span class="badge pending">awaiting</span>
            @endif
          </td>
          <td class="nw">{{ optional($c->signed_at)->format('M j, Y') ?: '—' }}</td>
          <td class="sub">
            @switch($c->source)
              @case('payment_link') Payment link @break
              @case('manual') Sent manually @break
              @default Checkout
            @endswitch
          </td>
          <td class="actions">
            <a class="adm-btn ghost" href="{{ route('admin.contracts.show', $c) }}">View</a>
            @if ($c->isSigned())
              <a class="adm-btn" href="{{ route('admin.contracts.pdf', $c) }}">PDF</a>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table></div>

  <div class="pager">
    <div>Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }}</div>
    <div class="links">{!! $rows->links('vendor.pagination.admin') !!}</div>
  </div>
@endif
@endsection
