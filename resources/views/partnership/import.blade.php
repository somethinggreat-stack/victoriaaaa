@extends('partnership.layout')
@section('title', 'Import Clients')
@section('subtitle', 'Load Burgundy\'s client list from a spreadsheet.')

@section('content')

@if($result)
  <div class="card" style="border-color:rgba(21,128,61,.3);background:var(--green-soft)">
    <h2 style="color:var(--green)">Import complete</h2>
    <div class="tiles" style="margin:14px 0 0">
      <div class="tile"><div class="k">Added</div><div class="v">{{ $result['created'] }}</div></div>
      <div class="tile"><div class="k">Updated</div><div class="v">{{ $result['updated'] }}</div></div>
      <div class="tile"><div class="k">Total clients</div><div class="v">{{ $result['total'] }}</div></div>
      @if($result['duplicates'] > 0)
        <div class="tile amber"><div class="k">To review</div><div class="v">{{ $result['duplicates'] }}</div><div class="n">Possible duplicates</div></div>
      @endif
    </div>

    @if(!empty($result['samples']))
      <p class="sub" style="margin:16px 0 0;color:var(--ink-2)">
        <strong>Skipped {{ count($result['samples']) }} Credit Repair Cloud demo record{{ count($result['samples']) === 1 ? '' : 's' }}</strong>
        — these ship with every CRC account and aren't real clients:
      </p>
      <ul style="margin:6px 0 0;font-size:12.5px;color:var(--ink-2)">
        @foreach($result['samples'] as $s)<li class="mono">{{ $s }}</li>@endforeach
      </ul>
    @endif

    <div style="margin-top:18px;display:flex;gap:9px;flex-wrap:wrap">
      <a href="{{ route('partnership.clients') }}" class="btn sm">View clients</a>
      @if($result['duplicates'] > 0)
        <a href="{{ route('partnership.review') }}" class="btn ghost sm">Review duplicates</a>
      @endif
    </div>
  </div>
@endif

<div class="card">
  <h2>Import the client list</h2>
  <p class="sub">
    Upload the spreadsheet exported from Credit Repair Cloud. You currently have
    <strong>{{ $clientCount }}</strong> client{{ $clientCount === 1 ? '' : 's' }} loaded.
  </p>

  <form method="POST" action="{{ route('partnership.import.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="field">
      <label class="fl">Client list (.csv)</label>
      <input type="file" name="csv" accept=".csv,text/csv" required
             style="padding:11px;border:1.5px dashed var(--line-2);border-radius:10px;background:var(--bg-2)">
      @error('csv')<div style="color:var(--red);font-size:12px;font-weight:600;margin-top:6px">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn">Import clients</button>
  </form>
</div>

<div class="card">
  <h2>What to expect</h2>

  <p class="sub" style="margin-bottom:14px">A few things this does on purpose, so nothing surprises you:</p>

  <div style="display:flex;flex-direction:column;gap:14px;font-size:13.5px;color:var(--ink-2)">
    <div>
      <strong style="color:var(--ink)">Safe to run more than once.</strong><br>
      Clients are matched on email, so re-importing updates existing records instead of creating
      duplicates. Contact statuses, notes and payments you've already recorded are never overwritten.
    </div>
    <div>
      <strong style="color:var(--ink)">Demo records are skipped.</strong><br>
      Credit Repair Cloud puts "Sample Client" and "Sample Lead" in every account, and they come
      along in exports. They're left out and listed by name above so you can see exactly what was skipped.
    </div>
    <div>
      <strong style="color:var(--ink)">Duplicates are flagged, not merged.</strong><br>
      Two rows sharing a date of birth and SSN last-4 are usually the same person listed twice — but
      not always. They're sent to <a href="{{ route('partnership.review') }}" style="text-decoration:underline">Needs Review</a>
      for you to confirm, because merging two real people is much harder to undo than an extra row.
    </div>
    <div>
      <strong style="color:var(--ink)">The file isn't stored.</strong><br>
      It's read once and discarded. Nothing containing dates of birth or SSN digits is written to disk
      or left anywhere reachable from the web.
    </div>
  </div>

  <h3 style="margin-top:22px;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:var(--ink-3)">Expected columns</h3>
  <p class="mono" style="font-size:11.5px;color:var(--ink-2);background:var(--bg-2);padding:11px;border-radius:9px;margin:0;overflow-x:auto">
    First Name, Middle Name, Last Name, Mobile Number, Email, Date of Birth, SSN Last 4 Digits, zip
  </p>
  <p class="sub" style="margin:9px 0 0;font-size:12px">
    Exporting from Excel or Google Sheets? Use <strong>File → Download → CSV</strong>.
  </p>
</div>

@endsection
