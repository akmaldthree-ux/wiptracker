@extends('layouts.app')
@section('title','Detail Handover')
@section('page-title','Detail Handover')
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small mb-0">
    <li class="breadcrumb-item"><a href="{{ route('handover.index') }}">Handover</a></li>
    <li class="breadcrumb-item active">{{ $handover->handover_no }}</li>
  </ol>
</nav>
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <div>
    <h4 class="fw-bold mb-1">{{ $handover->handover_no }} <span class="badge bg-{{ $handover->status_color }}">{{ $handover->status_label }}</span></h4>
    <p class="text-muted mb-0">{{ $handover->fromStation?->name ?? 'Order Produksi' }} → {{ $handover->toStation->name }} | Order: {{ $handover->order->order_no }}</p>
  </div>
  <div class="d-flex gap-2">
    @if(in_array(auth()->user()->role,['admin','supervisor']) && $handover->status === 'discrepancy')
    <form method="POST" action="{{ route('handover.approve',$handover) }}">
      @csrf
      <button type="submit" class="btn btn-success" onclick="return confirm('Setujui discrepancy ini?')"><i class="bi bi-check-circle me-2"></i>Setujui Discrepancy</button>
    </form>
    @endif
    @if($handover->toStation?->is_final
        && in_array($handover->status, ['confirmed','approved'])
        && $handover->order->status !== 'completed'
        && (in_array(auth()->user()->role,['admin','supervisor']) || auth()->user()->station_id == $handover->to_station_id))
    <form method="POST" action="{{ route('handover.complete-order',$handover) }}"
          onsubmit="return confirm('Selesaikan order {{ $handover->order->order_no }}? Semua WIP di stasiun ini akan ditutup dan order tidak bisa diaktifkan kembali.')">
      @csrf
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-flag-fill me-2"></i>Selesaikan Order
      </button>
    </form>
    @endif
    <a href="{{ route('handover.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>

@if(session('warning'))
<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}</div>
@endif

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Handover</div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" width="45%">No. Handover</td><td class="fw-semibold">{{ $handover->handover_no }}</td></tr>
          <tr><td class="text-muted">Order</td><td><a href="{{ route('orders.show',$handover->order) }}">{{ $handover->order->order_no }}</a></td></tr>
          <tr><td class="text-muted">Dari</td><td class="fw-semibold">{{ $handover->fromStation?->name ?? '📋 Order Produksi' }}</td></tr>
          <tr><td class="text-muted">Ke Stasiun</td><td class="fw-semibold">{{ $handover->toStation->name }}</td></tr>
          @if($handover->sewingLocation)
          <tr>
            <td class="text-muted">Tempat Sewing</td>
            <td>
              <span class="badge bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25">
                <i class="bi bi-building me-1"></i>{{ $handover->sewingLocation->name }}
              </span>
              @if($handover->sewingLocation->address)
              <br><small class="text-muted">{{ $handover->sewingLocation->address }}</small>
              @endif
            </td>
          </tr>
          @endif
          <tr><td class="text-muted">Diinisiasi Oleh</td><td>{{ $handover->initiatedBy->name }}</td></tr>
          <tr><td class="text-muted">Waktu Kirim</td><td>{{ $handover->initiated_at ? $handover->initiated_at->format('d M Y H:i') : '-' }}</td></tr>
          @if($handover->confirmedBy)
          <tr><td class="text-muted">Dikonfirmasi Oleh</td><td>{{ $handover->confirmedBy->name }}</td></tr>
          <tr><td class="text-muted">Waktu Konfirmasi</td><td>{{ $handover->confirmed_at ? $handover->confirmed_at->format('d M Y H:i') : '-' }}</td></tr>
          @endif
          @if($handover->approvedBy)
          <tr><td class="text-muted">Disetujui Oleh</td><td>{{ $handover->approvedBy->name }}</td></tr>
          @endif
          <tr><td class="text-muted">Kondisi Barang</td><td>{{ $handover->condition_notes ?? '-' }}</td></tr>
          <tr><td class="text-muted">Catatan</td><td>{{ $handover->notes ?? '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="row g-3 h-100">
      {{-- Ringkasan Qty --}}
      <div class="col-12">
        <div class="card">
          <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Ringkasan Qty</div>
          <div class="card-body">
            <div class="row g-2 text-center">
              <div class="col-4">
                <div class="stat-card">
                  <div class="stat-value text-primary">{{ $handover->total_sent }}</div>
                  <div class="stat-label">Dikirim</div>
                </div>
              </div>
              <div class="col-4">
                <div class="stat-card">
                  <div class="stat-value text-success">{{ $handover->total_received ?? '—' }}</div>
                  <div class="stat-label">Diterima</div>
                </div>
              </div>
              <div class="col-4">
                <div class="stat-card">
                  <div class="stat-value {{ ($handover->total_discrepancy ?? 0) != 0 ? 'text-danger' : 'text-success' }}">{{ $handover->total_discrepancy ?? '0' }}</div>
                  <div class="stat-label">Selisih</div>
                </div>
              </div>
            </div>
            @if($handover->hasDiscrepancy())
            <div class="alert alert-danger mt-3 mb-0 py-2 d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i>Ada discrepancy pada handover ini!</div>
            @endif
          </div>
        </div>
      </div>

      {{-- Foto Bukti --}}
      <div class="col-12">
        <div class="card">
          <div class="card-header"><i class="bi bi-camera me-2"></i>Foto Bukti</div>
          <div class="card-body">
            <div class="row g-2">
              <div class="col-6">
                <p class="text-muted mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px"><i class="bi bi-send me-1"></i>Foto Kirim</p>
                @if($handover->photo_sent)
                <a href="{{ asset($handover->photo_sent) }}" target="_blank">
                  <img src="{{ asset($handover->photo_sent) }}" alt="Foto Kirim"
                       style="width:100%;height:110px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;cursor:zoom-in">
                </a>
                @else
                <div style="width:100%;height:110px;border-radius:8px;border:2px dashed #e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8">
                  <i class="bi bi-image" style="font-size:1.5rem"></i>
                </div>
                @endif
              </div>
              <div class="col-6">
                <p class="text-muted mb-1" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px"><i class="bi bi-box-arrow-in-down me-1"></i>Foto Terima</p>
                @if($handover->photo_received)
                <a href="{{ asset($handover->photo_received) }}" target="_blank">
                  <img src="{{ asset($handover->photo_received) }}" alt="Foto Terima"
                       style="width:100%;height:110px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;cursor:zoom-in">
                </a>
                @else
                <div style="width:100%;height:110px;border-radius:8px;border:2px dashed #e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8">
                  <i class="bi bi-image" style="font-size:1.5rem"></i>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Items -->
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-list-ul me-2"></i>Detail Item Handover</div>

  @if($handover->status === 'pending' && auth()->user()->station_id == $handover->to_station_id)
  {{-- FORM KONFIRMASI --}}
  <form method="POST" action="{{ route('handover.confirm',$handover) }}" enctype="multipart/form-data">
    @csrf

    @if($errors->any())
    <div class="alert alert-danger mx-3 mt-3">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>SKU</th><th>Warna</th><th>Ukuran</th><th>Qty Kirim</th>
            <th>Qty Terima</th><th>Qty Reject</th><th>Tipe Reject</th><th>Foto Reject</th><th>Alasan Reject</th><th>Catatan Selisih</th>
          </tr>
        </thead>
        <tbody>
          @foreach($handover->items as $item)
          <tr>
            <td><small class="font-monospace">{{ $item->sku->sku_code }}</small></td>
            <td>{{ optional($item->sku->color)->name }}</td>
            <td>{{ optional($item->sku->size)->name }}</td>
            <td class="fw-semibold text-primary">{{ $item->qty_sent }}</td>
            <td style="width:110px">
              <input type="number" name="items[{{ $item->id }}][qty_received]"
                     class="form-control form-control-sm"
                     value="{{ $item->qty_sent }}" min="0" required>
            </td>
            <td style="width:100px">
              <input type="number" name="items[{{ $item->id }}][qty_reject]"
                     class="form-control form-control-sm qty-reject-input"
                     data-item-id="{{ $item->id }}" value="0" min="0">
            </td>
            <td style="min-width:130px">
              {{-- Tipe Reject --}}
              <div class="reject-type-wrap" id="rejectTypeWrap_{{ $item->id }}" style="display:none">
                <select name="items[{{ $item->id }}][reject_type]" class="form-select form-select-sm mb-1 reject-type-sel" data-item-id="{{ $item->id }}">
                  <option value="">-- Pilih Tipe --</option>
                  <option value="rework">🔧 Rework</option>
                  <option value="second">🏷️ Second</option>
                  <option value="scrap">🗑️ Scrap</option>
                </select>
                <div id="rejectTypeHint_{{ $item->id }}" class="form-text" style="font-size:.68rem"></div>
              </div>
              <small class="text-muted no-reject-label2" id="noRejectLabel2_{{ $item->id }}">—</small>
            </td>
            <td style="min-width:130px">
              <div class="photo-reject-wrap" id="photoRejectWrap_{{ $item->id }}" style="display:none">
                <input type="file" name="photo_reject_{{ $item->id }}"
                       class="form-control form-control-sm photo-reject-input"
                       accept="image/*" capture="environment" data-item-id="{{ $item->id }}">
                <div class="mt-1" id="photoRejectPreview_{{ $item->id }}" style="display:none">
                  <img src="" alt="" style="max-height:60px;border-radius:4px;border:1px solid #fca5a5;object-fit:cover">
                </div>
                <small class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>Wajib jika ada reject</small>
              </div>
              <small class="text-muted no-reject-label" id="noRejectLabel_{{ $item->id }}">—</small>
            </td>
            <td>
              <input type="text" name="items[{{ $item->id }}][reject_notes]"
                     class="form-control form-control-sm" placeholder="Alasan reject...">
            </td>
            <td>
              <input type="text" name="items[{{ $item->id }}][discrepancy_notes]"
                     class="form-control form-control-sm" placeholder="Catatan selisih...">
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Foto Penerimaan --}}
    <div class="p-3 mx-3 my-3 border border-success border-opacity-50 rounded" style="background:rgba(25,135,84,.05)">
      <label class="form-label fw-semibold text-success-emphasis">
        <i class="bi bi-camera-fill me-1 text-success"></i>Foto Bukti Penerimaan <span class="text-danger">*</span>
      </label>
      <input type="file" name="photo_received" id="photoReceivedInput"
             class="form-control @error('photo_received') is-invalid @enderror"
             accept="image/*" capture="environment" required>
      @error('photo_received')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text"><i class="bi bi-info-circle me-1"></i>Foto kondisi barang saat diterima. Maks 10MB. Akan dikompres otomatis.</div>
      <div id="photoReceivedPreview" class="mt-2" style="display:none">
        <img id="photoReceivedImg" src="" alt="Preview" style="max-height:180px;border-radius:8px;border:2px solid #198754;object-fit:cover">
      </div>
    </div>

    <div class="card-footer d-flex align-items-center gap-3">
      <button type="submit" class="btn btn-success" onclick="this.disabled=true;this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\' role=\'status\'></span>Memproses...';this.form.submit()">
        <i class="bi bi-check-circle me-2"></i>Konfirmasi Penerimaan
      </button>
      <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Pastikan semua data sudah benar sebelum konfirmasi</small>
    </div>
  </form>

  @else
  {{-- TAMPILAN READ-ONLY --}}
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>SKU</th><th>Warna</th><th>Ukuran</th><th>Qty Kirim</th><th>Qty Terima</th><th>Qty Reject</th><th>Tipe Reject</th><th>Foto Reject</th><th>Selisih</th><th>Catatan</th></tr>
      </thead>
      <tbody>
        @foreach($handover->items as $item)
        <tr class="{{ $item->discrepancy != 0 && $item->discrepancy !== null ? 'table-warning' : '' }}">
          <td><small class="font-monospace">{{ $item->sku->sku_code }}</small></td>
          <td>{{ optional($item->sku->color)->name }}</td>
          <td>{{ optional($item->sku->size)->name }}</td>
          <td class="fw-semibold">{{ $item->qty_sent }}</td>
          <td class="fw-semibold {{ $item->qty_received !== null ? 'text-success' : 'text-muted' }}">{{ $item->qty_received ?? '-' }}</td>
          <td class="{{ ($item->qty_reject ?? 0) > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
            {{ ($item->qty_reject ?? 0) > 0 ? $item->qty_reject : '-' }}
            @if($item->reject_notes)<br><small class="text-muted">{{ $item->reject_notes }}</small>@endif
          </td>
          <td>
            @if($item->reject_type)
              @php $rt = $item->reject_type; @endphp
              <span class="badge {{ $rt==='rework'?'bg-warning text-dark':($rt==='second'?'bg-info text-white':'bg-danger text-white') }}">
                {{ $rt==='rework'?'🔧 Rework':($rt==='second'?'🏷️ Second':'🗑️ Scrap') }}
              </span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            @if($item->photo_reject)
            <a href="{{ asset($item->photo_reject) }}" target="_blank">
              <img src="{{ asset($item->photo_reject) }}" alt="Foto Reject"
                   style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:2px solid #fca5a5;cursor:zoom-in">
            </a>
            @else
            <span class="text-muted">—</span>
            @endif
          </td>
          <td class="{{ $item->discrepancy != 0 && $item->discrepancy !== null ? 'text-danger fw-bold' : 'text-muted' }}">{{ $item->discrepancy !== null ? $item->discrepancy : '-' }}</td>
          <td><small class="text-muted">{{ $item->discrepancy_notes ?? '-' }}</small></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
const rejectTypeHints = {
  rework: '🔧 Barang dikembalikan ke stasiun asal untuk diperbaiki. Handover rework otomatis dibuat.',
  second: '🏷️ Barang disimpan sebagai produk second quality.',
  scrap:  '🗑️ Barang dihapus dari sistem sebagai kerugian produksi.',
};

// Toggle tipe reject & foto reject per item berdasarkan qty_reject
document.querySelectorAll('.qty-reject-input').forEach(input => {
  input.addEventListener('input', function() {
    const id        = this.dataset.itemId;
    const typeWrap  = document.getElementById('rejectTypeWrap_' + id);
    const lbl2      = document.getElementById('noRejectLabel2_' + id);
    const photoWrap = document.getElementById('photoRejectWrap_' + id);
    const lbl       = document.getElementById('noRejectLabel_' + id);
    const fileInput = photoWrap ? photoWrap.querySelector('.photo-reject-input') : null;
    const typeSel   = typeWrap ? typeWrap.querySelector('.reject-type-sel') : null;

    if (parseInt(this.value) > 0) {
      if (typeWrap)  { typeWrap.style.display = 'block'; }
      if (lbl2)      { lbl2.style.display = 'none'; }
      if (photoWrap) { photoWrap.style.display = 'block'; }
      if (lbl)       { lbl.style.display = 'none'; }
      if (fileInput) fileInput.setAttribute('required', 'required');
      if (typeSel)   typeSel.setAttribute('required', 'required');
    } else {
      if (typeWrap)  { typeWrap.style.display = 'none'; }
      if (lbl2)      { lbl2.style.display = ''; }
      if (photoWrap) { photoWrap.style.display = 'none'; }
      if (lbl)       { lbl.style.display = ''; }
      if (fileInput) { fileInput.removeAttribute('required'); fileInput.value = ''; }
      if (typeSel)   { typeSel.removeAttribute('required'); typeSel.value = ''; }
      const prev = document.getElementById('photoRejectPreview_' + id);
      if (prev) prev.style.display = 'none';
    }
  });
});

// Hint teks per tipe reject
document.querySelectorAll('.reject-type-sel').forEach(sel => {
  sel.addEventListener('change', function() {
    const hint = document.getElementById('rejectTypeHint_' + this.dataset.itemId);
    if (hint) hint.textContent = rejectTypeHints[this.value] || '';
  });
});

// Preview foto reject per item
document.querySelectorAll('.photo-reject-input').forEach(input => {
  input.addEventListener('change', function() {
    const id   = this.dataset.itemId;
    const prev = document.getElementById('photoRejectPreview_' + id);
    const img  = prev ? prev.querySelector('img') : null;
    if (!this.files[0] || !prev || !img) return;
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; prev.style.display = 'block'; };
    reader.readAsDataURL(this.files[0]);
  });
});

// Preview foto terima
const photoReceivedInput = document.getElementById('photoReceivedInput');
if (photoReceivedInput) {
  photoReceivedInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('photoReceivedImg').src = e.target.result;
      document.getElementById('photoReceivedPreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
  });
}
</script>
@endpush
