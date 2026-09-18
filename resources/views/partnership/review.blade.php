@extends('partnership.layout')
@section('title', 'Needs Review')
@section('subtitle', 'Payments and records that need a human decision.')

@section('content')

{{-- ── Unmatched payments ── --}}
<div class="card">
  <h2>Unmatched payments</h2>
  <p class="sub">
    These people paid through the $100 link, but their email, phone and name didn't match anyone on the list.
    Either they're new, or they paid using details we didn't have on file.
  </p>

  @if($unmatched->isEmpty())
    <div class="empty"><strong>Nothing to review</strong>Every payment has been matched to a client.</div>
  @else
    @foreach($unmatched as $c)
      <div style="border:1px solid var(--line-2);border-radius:var(--r);padding:16px;margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:13px">
          <div>
            <div style="font-size:15px;font-weight:700">{{ $c->full_name }}</div>
            <div class="mono mut" style="font-size:12px">{{ $c->email ?: 'no email' }} · {{ $c->phone ?: 'no phone' }}</div>
            @if($c->subscription)
              <div style="margin-top:7px">
                <span class="pill green">Paid ${{ number_format((float) $c->subscription->amount, 2) }}</span>
                <span class="mut" style="font-size:11.5px;margin-left:6px">{{ $c->subscription->subscribed_at?->format('M j, Y') }}</span>
              </div>
            @endif
          </div>
          <a href="{{ route('partnership.clients.show', $c) }}" class="btn ghost sm" style="align-self:start">View record</a>
        </div>

        <div style="display:flex;gap:11px;flex-wrap:wrap;align-items:end;padding-top:13px;border-top:1px solid var(--line)">
          <form method="POST" action="{{ route('partnership.review.merge', $c) }}" style="display:flex;gap:9px;align-items:end;flex:1;min-width:280px">
            @csrf
            <div style="flex:1">
              <label class="fl">This is actually an existing client</label>
              <input type="text" class="client-search" data-target="target-{{ $c->id }}" placeholder="Start typing a name…" autocomplete="off">
              <input type="hidden" name="target_id" id="target-{{ $c->id }}">
              <div class="results" style="position:relative"></div>
            </div>
            <button type="submit" class="btn">Merge</button>
          </form>

          <form method="POST" action="{{ route('partnership.review.confirm', $c) }}">
            @csrf
            <button type="submit" class="btn ghost">Confirm as new client</button>
          </form>
        </div>
      </div>
    @endforeach
  @endif
</div>

{{-- ── Possible duplicates ── --}}
<div class="card">
  <h2>Possible duplicates</h2>
  <p class="sub">
    These records share a date of birth and SSN last-4 with another, which usually means the same person
    was listed twice. They were <strong>not</strong> merged automatically — merging two real people is much
    harder to undo than keeping an extra row.
  </p>

  @if($duplicates->isEmpty())
    <div class="empty"><strong>No duplicates flagged</strong></div>
  @else
    @foreach($duplicates as $c)
      @php $other = $c->duplicateOf; @endphp
      @continue(!$other)
      <div style="border:1px solid var(--line-2);border-radius:var(--r);padding:16px;margin-bottom:14px">
        <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:16px;align-items:center;margin-bottom:14px">
          <div>
            <div style="font-weight:700">{{ $other->full_name }}</div>
            <div class="mono mut" style="font-size:12px">{{ $other->email ?: '—' }}</div>
            <div class="mono mut" style="font-size:12px">{{ $other->phone ?: 'no phone' }}</div>
            @if($other->hasPaid())<span class="pill green" style="margin-top:6px">Paid</span>@endif
          </div>
          <div class="mut" style="font-size:19px">↔</div>
          <div>
            <div style="font-weight:700">{{ $c->full_name }}</div>
            <div class="mono mut" style="font-size:12px">{{ $c->email ?: '—' }}</div>
            <div class="mono mut" style="font-size:12px">{{ $c->phone ?: 'no phone' }}</div>
            @if($c->hasPaid())<span class="pill green" style="margin-top:6px">Paid</span>@endif
          </div>
        </div>

        <div style="display:flex;gap:9px;flex-wrap:wrap;padding-top:13px;border-top:1px solid var(--line)">
          <form method="POST" action="{{ route('partnership.review.merge', $c) }}">
            @csrf
            <input type="hidden" name="target_id" value="{{ $other->id }}">
            <button type="submit" class="btn">Same person — merge into {{ $other->first_name }}</button>
          </form>
          <form method="POST" action="{{ route('partnership.review.not-duplicate', $c) }}">
            @csrf
            <button type="submit" class="btn ghost">Different people</button>
          </form>
          <a href="{{ route('partnership.clients.show', $c) }}" class="btn ghost">View record</a>
        </div>
      </div>
    @endforeach
  @endif
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.client-search').forEach(function (input) {
  var box = input.parentElement.querySelector('.results');
  var hidden = document.getElementById(input.dataset.target);
  var timer;

  input.addEventListener('input', function () {
    clearTimeout(timer);
    hidden.value = '';
    var q = input.value.trim();
    if (q.length < 2) { box.innerHTML = ''; return; }

    timer = setTimeout(function () {
      fetch('{{ route('partnership.review.search') }}?q=' + encodeURIComponent(q))
        .then(function (r) { return r.json(); })
        .then(function (rows) {
          if (!rows.length) { box.innerHTML = '<div style="padding:8px;color:#938589;font-size:12px">No match</div>'; return; }
          box.innerHTML = '<div style="position:absolute;z-index:20;background:#fff;border:1px solid rgba(26,18,20,.16);border-radius:10px;width:100%;max-height:230px;overflow:auto;box-shadow:0 12px 26px -12px rgba(0,0,0,.28)">' +
            rows.map(function (r) {
              return '<div class="opt" data-id="' + r.id + '" data-label="' + r.label.replace(/"/g, '&quot;') + '" ' +
                     'style="padding:9px 11px;cursor:pointer;border-bottom:1px solid rgba(26,18,20,.07)">' +
                     '<div style="font-weight:600;font-size:13px">' + r.label + '</div>' +
                     '<div style="font-size:11px;color:#938589;font-family:ui-monospace,monospace">' + (r.email || '—') + '</div>' +
                     '</div>';
            }).join('') + '</div>';

          box.querySelectorAll('.opt').forEach(function (opt) {
            opt.addEventListener('mouseenter', function () { opt.style.background = '#fff6f8'; });
            opt.addEventListener('mouseleave', function () { opt.style.background = '#fff'; });
            opt.addEventListener('click', function () {
              input.value = opt.dataset.label;
              hidden.value = opt.dataset.id;
              box.innerHTML = '';
            });
          });
        });
    }, 220);
  });
});

// A merge with nothing picked would be a no-op at best — stop it at the form.
document.querySelectorAll('form[action*="/merge"]').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    var hidden = form.querySelector('input[name=target_id]');
    if (hidden && !hidden.value) {
      e.preventDefault();
      alert('Pick the existing client to merge into first.');
    }
  });
});
</script>
@endpush
