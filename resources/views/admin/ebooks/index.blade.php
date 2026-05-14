@extends('admin.layout')
@section('title', 'eBooks Catalog')

@section('content')
<div class="admin-header">
  <div>
    <h1>eBooks Catalog</h1>
    <div class="sub">{{ $ebooks->count() }} ebook{{ $ebooks->count() === 1 ? '' : 's' }} · click an ebook to edit its Drive download link, price, and status.</div>
  </div>
  <a href="{{ route('admin.ebook-orders') }}" class="adm-btn">View sales →</a>
</div>

<style>
.eb-cat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 18px;
}
.eb-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 22px 22px 18px;
  display: flex; flex-direction: column;
  gap: 14px;
  position: relative;
  transition: transform .2s, box-shadow .2s, border-color .2s;
}
.eb-card:hover { transform: translateY(-3px); border-color: var(--pink); box-shadow: 0 20px 36px -16px rgba(230,49,121,0.22); }
.eb-card .row {
  display: grid; grid-template-columns: 80px 1fr; gap: 14px; align-items: center;
}
.eb-card .cover {
  width: 80px;
  border-radius: 6px;
  overflow: hidden;
  box-shadow: 0 10px 20px -10px rgba(20,16,14,0.4);
}
.eb-card .cover img { width: 100%; display: block; }
.eb-card h3 {
  font-size: 14.5px; font-weight: 700; margin: 0 0 4px;
  letter-spacing: -0.005em; line-height: 1.25;
}
.eb-card .price {
  display: inline-block;
  font-size: 13px; font-weight: 700; color: var(--pink);
  background: var(--pink-soft);
  padding: 3px 9px; border-radius: 100px;
}
.eb-card .status-row {
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
  font-size: 12px;
  padding-top: 12px;
  border-top: 1px dashed var(--line);
  color: var(--ink-3);
}
.eb-card .status-row .pill {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 4px 10px;
  border-radius: 100px;
  font-size: 10.5px; font-weight: 700;
  letter-spacing: 0.08em; text-transform: uppercase;
}
.eb-card .status-row .pill.ok   { background: rgba(34,197,94,0.10); color: #157a3d; }
.eb-card .status-row .pill.warn { background: #fef3c7; color: #92400e; }
.eb-card .status-row .pill.off  { background: var(--bg-2); color: var(--ink-3); }
.eb-card .stats {
  display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
}
.eb-card .stats .s {
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 10px 12px;
}
.eb-card .stats .s .lab { font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--ink-3); font-weight: 700; }
.eb-card .stats .s .val { font-size: 16px; font-weight: 700; color: var(--ink); letter-spacing: -0.01em; margin-top: 3px; }
.eb-card .stats .s.alt .val { color: #157a3d; }

.eb-card .actions {
  display: flex; gap: 8px;
}
.eb-card .actions a {
  flex: 1;
  text-align: center;
  font-size: 12.5px; font-weight: 600;
  padding: 9px 12px;
  border-radius: 100px;
}
.eb-card .actions .edit    { background: var(--ink); color: #fff; }
.eb-card .actions .edit:hover { background: var(--pink); }
.eb-card .actions .preview { background: transparent; color: var(--ink-2); border: 1px solid var(--line-2); }
.eb-card .actions .preview:hover { color: var(--pink); border-color: var(--pink); }
</style>

<div class="eb-cat-grid">
  @foreach($ebooks as $ebook)
    @php
      $s = $sales->get($ebook->slug);
      $orderCount = $s ? (int) $s->orders   : 0;
      $revenue    = $s ? (float) $s->revenue : 0.0;
    @endphp
    <div class="eb-card">
      <div class="row">
        <div class="cover">
          @if($ebook->cover_image)
            <img src="{{ asset($ebook->cover_image) }}" alt="{{ $ebook->title }}" />
          @endif
        </div>
        <div>
          <h3>{{ $ebook->title }}</h3>
          <span class="price">${{ number_format((float) $ebook->price, 2) }}</span>
        </div>
      </div>

      <div class="stats">
        <div class="s">
          <div class="lab">Orders</div>
          <div class="val">{{ number_format($orderCount) }}</div>
        </div>
        <div class="s alt">
          <div class="lab">Revenue</div>
          <div class="val">${{ number_format($revenue, 2) }}</div>
        </div>
      </div>

      <div class="status-row">
        @if($ebook->is_active)
          <span class="pill ok">● Active</span>
        @else
          <span class="pill off">● Hidden</span>
        @endif

        @if($ebook->drive_link)
          <span class="pill ok">✓ Drive link set</span>
        @else
          <span class="pill warn">⚠ No Drive link</span>
        @endif
      </div>

      <div class="actions">
        <a href="{{ route('admin.ebooks.edit', $ebook) }}" class="edit">Edit</a>
        <a href="{{ route('ebooks.checkout', $ebook->slug) }}" class="preview" target="_blank">Preview checkout ↗</a>
      </div>
    </div>
  @endforeach
</div>

@endsection
