@extends('partnership.layout')
@section('title', $client->full_name)
@section('subtitle', 'Client record')

@section('actions')
  <a href="{{ route('partnership.clients') }}" class="btn ghost">← All clients</a>
@endsection

@section('content')

@php $sub = $client->subscription; $ob = $client->onboardingSubmission; @endphp

@if($client->needsReview())
  <div class="card" style="border-color:rgba(180,83,9,.3);background:var(--amber-soft)">
    <h2 style="color:var(--amber)">This payment wasn't matched automatically</h2>
    <p class="sub" style="color:var(--amber)">Confirm whether this is a new client, or the same person as an existing record.</p>
    <a href="{{ route('partnership.review') }}" class="btn sm">Resolve in review queue</a>
  </div>
@endif

@if($duplicate)
  <div class="card" style="border-color:rgba(180,83,9,.3);background:var(--amber-soft)">
    <h2 style="color:var(--amber)">Possible duplicate</h2>
    <p class="sub" style="color:var(--amber)">
      Shares a date of birth and SSN last-4 with
      <a href="{{ route('partnership.clients.show', $duplicate) }}" style="text-decoration:underline"><strong>{{ $duplicate->full_name }}</strong></a>.
      If they're the same person, merge them so payments from either email match one record.
    </p>
    <a href="{{ route('partnership.review') }}" class="btn sm">Resolve in review queue</a>
  </div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">
  <div style="min-width:0">

    {{-- Where they are ── --}}
    <div class="card">
      <h2>Pipeline</h2>
      <p class="sub">Contact status is set by hand. Everything after payment advances on its own.</p>
      <div style="display:flex;gap:9px;flex-wrap:wrap;margin-bottom:6px">
        @include('partnership.partials.stage-pill', ['client' => $client])
        @include('partnership.partials.payment-pill', ['client' => $client])
        @if($client->onboarding_status === 'complete')<span class="pill green">Onboarded</span>@endif
        @if($client->apex_status === 'sent')<span class="pill green">Sent to Apex</span>
        @elseif($client->apex_status === 'failed')<span class="pill red">Apex delivery failed</span>@endif
        @if($client->matched_on === 'name')<span class="pill amber">Matched on name only</span>@endif
      </div>
    </div>

    {{-- Payments ── --}}
    <div class="card">
      <h2>Payments</h2>
      <p class="sub">Updated automatically by Authorize.Net — rebills, failures and refunds all land here.</p>

      @if($payments->isEmpty())
        <div class="empty"><strong>No payments yet</strong>Nothing has been charged for this client.</div>
      @else
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Status</th><th>Transaction</th></tr></thead>
            <tbody>
            @foreach($payments as $p)
              <tr>
                <td>{{ $p->charged_at?->format('M j, Y') ?: '—' }}</td>
                <td>{{ ucfirst($p->type) }}</td>
                <td class="{{ in_array($p->type, ['refund','void']) ? '' : '' }}" style="font-weight:700;{{ in_array($p->type,['refund','void']) ? 'color:var(--red)' : '' }}">
                  {{ in_array($p->type, ['refund','void']) ? '−' : '' }}${{ number_format((float) $p->amount, 2) }}
                </td>
                <td>
                  @php $tone = match($p->status) { 'captured' => 'green', 'failed' => 'red', 'refunded','voided' => 'amber', default => 'grey' }; @endphp
                  <span class="pill {{ $tone }}">{{ ucfirst($p->status) }}</span>
                </td>
                <td class="mono mut">{{ $p->transaction_id ?: '—' }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    {{-- Onboarding ── --}}
    <div class="card">
      <h2>Onboarding</h2>
      @if($ob)
        <p class="sub">Submitted {{ $ob->created_at->format('M j, Y \a\t g:ia') }}.</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px">
          <div><div class="k mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Full name</div><div>{{ $ob->full_name }}</div></div>
          <div><div class="k mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Date of birth</div><div>{{ $ob->birth_date?->format('M j, Y') ?: '—' }}</div></div>
          <div><div class="k mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">SSN</div><div class="mono">{{ $ob->masked_ssn }}</div></div>
          <div><div class="k mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Phone</div><div class="mono">{{ $ob->phone }}</div></div>
          <div style="grid-column:1/-1"><div class="k mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Address</div><div>{{ $ob->street_address }}, {{ $ob->city }}, {{ $ob->state }} {{ $ob->zip }}</div></div>
        </div>
      @elseif($client->hasPaid())
        <div class="empty"><strong>Paid, but the form isn't done</strong>They still need to complete onboarding before Apex can start work.</div>
      @else
        <div class="empty"><strong>Not started</strong>Onboarding opens after payment.</div>
      @endif
    </div>

    {{-- Timeline ── --}}
    <div class="card">
      <h2>History</h2>
      <p class="sub">Everything that has happened to this record.</p>
      @if($client->events->isEmpty())
        <div class="empty"><strong>No history yet</strong></div>
      @else
        <div style="display:flex;flex-direction:column;gap:12px">
          @foreach($client->events as $e)
            <div style="display:flex;gap:11px;padding-bottom:12px;border-bottom:1px solid var(--line)">
              <div style="width:7px;height:7px;border-radius:50%;background:var(--wine-2);margin-top:6px;flex-shrink:0"></div>
              <div style="min-width:0">
                <div style="font-weight:600">{{ $e->note ?: ucfirst(str_replace('_', ' ', $e->event_type)) }}</div>
                <div class="mut" style="font-size:11.5px">{{ $e->created_at->format('M j, Y \a\t g:ia') }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- ── Sidebar ── --}}
  <div>
    <div class="card">
      <h2>Details</h2>
      <div style="display:flex;flex-direction:column;gap:11px;font-size:13px">
        <div><div class="mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Email</div><div class="mono">{{ $client->email ?: '—' }}</div></div>
        <div><div class="mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Phone</div><div class="mono">{{ $client->phone ?: '—' }}</div></div>
        <div><div class="mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Source</div><div>{{ $client->source === 'legacy' ? 'Legacy client list' : 'Added later' }}</div></div>
        @if($sub)
          <div><div class="mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Monthly</div><div>${{ number_format((float) ($sub->recurring_amount ?? 0), 2) }}</div></div>
          <div><div class="mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Next payment</div><div>{{ $client->nextBillingDate()?->format('M j, Y') ?: '—' }}</div></div>
          <div><div class="mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Started</div><div>{{ $sub->subscribed_at?->format('M j, Y') ?: '—' }}</div></div>
        @endif
        @if($client->agreement)
          <div>
            <div class="mut" style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:700">Agreement</div>
            <div><span class="pill green">Signed {{ $client->agreement->signed_at?->format('M j, Y') }}</span></div>
          </div>
        @endif
      </div>
    </div>

    <div class="card">
      <h2>Contact status &amp; notes</h2>
      <form method="POST" action="{{ route('partnership.clients.update', $client) }}">
        @csrf @method('PATCH')
        <div class="field">
          <label class="fl">Contact status</label>
          <select name="contact_status">
            @foreach($statuses as $key => $label)
              <option value="{{ $key }}" @selected($client->contact_status === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label class="fl">Notes</label>
          <textarea name="notes" rows="5" placeholder="What did they say?">{{ $client->notes }}</textarea>
        </div>
        <button type="submit" class="btn" style="width:100%">Save</button>
      </form>
    </div>

    @if(!$client->hasPaid())
      <div class="card">
        <h2>Send the checkout link</h2>
        <p class="sub">$100 today, then $100/month.</p>
        <input type="text" readonly value="{{ route('burgundy.checkout.show') }}" onclick="this.select()" class="mono" style="margin-bottom:9px">
        <p class="mut" style="font-size:11.5px;margin:0">Mark them <strong>Payment Link Sent</strong> once you've sent it.</p>
      </div>
    @endif
  </div>
</div>

@endsection
