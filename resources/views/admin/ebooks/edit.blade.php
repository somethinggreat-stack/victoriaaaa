@extends('admin.layout')
@section('title', 'Edit eBook · ' . $ebook->title)

@section('content')
<div class="admin-header">
  <div>
    <h1>Edit eBook</h1>
    <div class="sub">{{ $ebook->title }} · slug <code style="font-family:'SF Mono',Menlo,monospace;font-size:12px;color:var(--ink-3)">{{ $ebook->slug }}</code></div>
  </div>
  <a href="{{ route('admin.ebooks') }}" class="adm-btn ghost">← Back to catalog</a>
</div>

@if ($errors->any())
  <div class="flash error">
    <strong>Please fix:</strong>
    <ul style="margin:6px 0 0 18px">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<style>
.eb-edit-grid {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 24px;
  align-items: start;
}
@media (max-width: 900px) {
  .eb-edit-grid { grid-template-columns: 1fr; }
}
.eb-edit-side {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 22px 22px 24px;
  position: sticky; top: 80px;
}
.eb-edit-side .cover {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 24px 44px -16px rgba(20,16,14,0.4);
  margin-bottom: 14px;
}
.eb-edit-side .cover img { width: 100%; display: block; }
.eb-edit-side .ttl {
  font-family: 'Instrument Serif', serif; font-style: italic;
  font-size: 18px; line-height: 1.2;
  letter-spacing: -0.01em;
  color: var(--ink);
  margin: 0 0 6px;
}
.eb-edit-side .meta { font-size: 12px; color: var(--ink-3); margin: 0; }

.eb-edit-side .live-link {
  display: block;
  margin-top: 14px;
  padding: 9px 12px;
  background: var(--bg);
  border: 1px dashed var(--line-2);
  border-radius: 10px;
  font-size: 11.5px; color: var(--ink-3);
  text-align: center;
  word-break: break-all;
}
.eb-edit-side .live-link a { color: var(--pink); font-weight: 600; }

.eb-form {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--r-md);
  padding: 26px 28px 24px;
}
.eb-form .ef-field { margin-bottom: 18px; }
.eb-form .ef-field label {
  display: block;
  font-size: 11px; font-weight: 700;
  letter-spacing: 0.12em; text-transform: uppercase;
  color: var(--ink-2);
  margin-bottom: 8px;
}
.eb-form .ef-field input[type="text"],
.eb-form .ef-field input[type="url"],
.eb-form .ef-field input[type="number"],
.eb-form .ef-field textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid var(--line-2);
  border-radius: 10px;
  font-family: inherit;
  font-size: 14px;
  background: #fff;
  color: var(--ink);
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}
.eb-form .ef-field input:focus,
.eb-form .ef-field textarea:focus {
  border-color: var(--pink);
  box-shadow: 0 0 0 4px rgba(230,49,121,0.10);
}
.eb-form .ef-field textarea { min-height: 140px; resize: vertical; font-family: 'SF Mono', Menlo, monospace; font-size: 13px; }
.eb-form .ef-help {
  margin-top: 6px;
  font-size: 12px; color: var(--ink-3);
  line-height: 1.5;
}
.eb-form .ef-help.warn { color: #92400e; }
.eb-form .ef-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 600px) { .eb-form .ef-row { grid-template-columns: 1fr; } }

.eb-form .ef-toggle {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 16px;
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: 12px;
  cursor: pointer;
}
.eb-form .ef-toggle input { width: 18px; height: 18px; accent-color: var(--pink); }
.eb-form .ef-toggle .info { flex: 1; }
.eb-form .ef-toggle .info strong { display: block; font-size: 13.5px; color: var(--ink); }
.eb-form .ef-toggle .info small { font-size: 12px; color: var(--ink-3); }

.eb-form .submit-row {
  display: flex; gap: 10px; align-items: center;
  margin-top: 22px;
  padding-top: 22px;
  border-top: 1px solid var(--line);
}
.eb-form .submit-row .save-btn {
  background: var(--pink);
  color: #fff;
  font-family: inherit; font-size: 14px; font-weight: 700;
  padding: 12px 26px;
  border: 0;
  border-radius: 100px;
  cursor: pointer;
  box-shadow: 0 14px 28px -12px rgba(230,49,121,0.55);
  transition: background .2s, transform .2s;
}
.eb-form .submit-row .save-btn:hover { background: var(--ink); transform: translateY(-2px); }

.drive-warn {
  background: #fef3c7;
  border: 1px solid #fbd97a;
  border-radius: 12px;
  padding: 14px 16px;
  font-size: 13px;
  color: #5c3a02;
  line-height: 1.55;
  margin-bottom: 22px;
}
.drive-warn strong { color: #7c2d12; }
</style>

<div class="eb-edit-grid">

  <!-- ── Preview / side card ─────────────────── -->
  <aside class="eb-edit-side">
    @if($ebook->cover_image)
      <div class="cover">
        <img src="{{ asset($ebook->cover_image) }}" alt="{{ $ebook->title }}" />
      </div>
    @endif
    <h3 class="ttl">{{ $ebook->title }}</h3>
    <p class="meta">Current price: <strong>${{ number_format((float) $ebook->price, 2) }}</strong></p>
    <p class="meta">Status:
      @if($ebook->is_active)
        <strong style="color:#157a3d">● Active</strong>
      @else
        <strong style="color:var(--ink-3)">● Hidden</strong>
      @endif
    </p>
    <p class="meta">Drive link:
      @if($ebook->drive_link)
        <strong style="color:#157a3d">✓ Set</strong>
      @else
        <strong style="color:#92400e">⚠ Not set</strong>
      @endif
    </p>

    <div class="live-link">
      Live checkout URL:<br>
      <a href="{{ route('ebooks.checkout', $ebook->slug) }}" target="_blank">{{ route('ebooks.checkout', $ebook->slug) }}</a>
    </div>
  </aside>

  <!-- ── Edit form ─────────────────────────── -->
  <form class="eb-form" method="POST" action="{{ route('admin.ebooks.update', $ebook) }}">
    @csrf
    @method('PATCH')

    @if(!$ebook->drive_link)
      <div class="drive-warn">
        <strong>This ebook has no Google Drive link yet.</strong><br>
        Until you paste one below, customers who buy this ebook will see a "Your download is being prepared" message on the thank-you page (instead of a direct link). Add the link, save, and future buyers will be redirected automatically.
      </div>
    @endif

    <div class="ef-field">
      <label>Google Drive download link</label>
      <input type="url" name="drive_link" value="{{ old('drive_link', $ebook->drive_link) }}"
             placeholder="https://drive.google.com/file/d/…/view?usp=sharing" />
      <div class="ef-help">
        Paste the shareable Google Drive link to the PDF. Make sure the file is shared as <strong>"Anyone with the link can view"</strong> in Drive — otherwise buyers will be blocked. After payment we redirect the buyer here automatically.
      </div>
    </div>

    <div class="ef-field">
      <label>Title</label>
      <input type="text" name="title" value="{{ old('title', $ebook->title) }}" required maxlength="200" />
    </div>

    <div class="ef-field">
      <label>Subtitle / one-line pitch</label>
      <input type="text" name="subtitle" value="{{ old('subtitle', $ebook->subtitle) }}" maxlength="255" />
      <div class="ef-help">Shown under the title on the checkout page.</div>
    </div>

    <div class="ef-row">
      <div class="ef-field">
        <label>Price (USD)</label>
        <input type="number" name="price" value="{{ old('price', number_format((float) $ebook->price, 2, '.', '')) }}"
               step="0.01" min="0" max="9999.99" required />
      </div>
      <div class="ef-field" style="display:flex;flex-direction:column;justify-content:flex-end">
        <label class="ef-toggle" style="margin-bottom:0">
          <input type="hidden" name="is_active" value="0" />
          <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ebook->is_active)) />
          <div class="info">
            <strong>Active</strong>
            <small>Visible on the homepage and accepting orders.</small>
          </div>
        </label>
      </div>
    </div>

    <div class="ef-field">
      <label>Features (one per line)</label>
      <textarea name="features" placeholder="The 90-day funding stack — step by step&#10;How to position your profile for lender approval">{{ old('features', is_array($ebook->features) ? implode("\n", $ebook->features) : '') }}</textarea>
      <div class="ef-help">Up to 12 bullets — these show on the checkout sidebar so buyers know what they're getting.</div>
    </div>

    <div class="submit-row">
      <button type="submit" class="save-btn">Save changes</button>
      <a href="{{ route('admin.ebooks') }}" style="font-size:13px;color:var(--ink-3)">Cancel</a>
    </div>
  </form>
</div>

@endsection
