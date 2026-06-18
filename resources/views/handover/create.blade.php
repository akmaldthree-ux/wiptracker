@extends('layouts.app')
@section('title','Buat Handover')
@section('page-title','Buat Handover Baru')
@section('content')
<div class="card" style="max-width:800px">
  <div class="card-header"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Form Handover Digital</div>
  <div class="card-body">
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Sistem akan otomatis menentukan stasiun tujuan berdasarkan urutan alur produksi.</div>

    @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('handover.store') }}" id="handoverForm">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Order Produksi <span class="text-danger">*</span></label>
          <select name="production_order_id" id="orderSel" class="form-select" required>
            <option value="">-- Pilih Order --</option>
            @foreach($orders as $o)
            <option value="{{ $o->id }}" {{ (request('order_id')==$o->id?'selected':'') }}>{{ $o->order_no }} — {{ $o->product->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Stasiun Asal <span class="text-danger">*</span></label>
          <select name="from_station_id" id="stationSel" class="form-select" required>
            <option value="">-- Pilih Stasiun --</option>
            @foreach($stations as $st)
            <option value="{{ $st->id }}" data-seq="{{ $st->order_sequence }}" {{ optional($station)->id==$st->id?'selected':'' }}>{{ $st->name }}</option>
            @endforeach
          </select>
          <div id="toStationInfo" class="form-text text-primary fw-semibold mt-1" style="display:none"></div>
        </div>

        {{-- Field Tempat Sewing: muncul saat asal adalah Cutting (menuju Sewing) atau asal adalah Sewing --}}
        <div class="col-12" id="sewingLocationWrapper" style="display:none">
          <div class="p-3 border border-primary border-opacity-25 rounded bg-primary bg-opacity-5">
            <label class="form-label fw-semibold text-primary">
              <i class="bi bi-building me-1"></i>Tempat Sewing Tujuan <span class="text-danger">*</span>
            </label>
            <select name="sewing_location_id" id="sewingLocationSel" class="form-select @error('sewing_location_id') is-invalid @enderror">
              <option value="">-- Pilih Tempat Sewing --</option>
              @foreach($sewingLocations as $loc)
              <option value="{{ $loc->id }}" {{ old('sewing_location_id') == $loc->id ? 'selected' : '' }}>
                [{{ $loc->code }}] {{ $loc->name }}
                @if($loc->capacity) — Kapasitas: {{ number_format($loc->capacity) }} pcs/hari @endif
              </option>
              @endforeach
            </select>
            @error('sewing_location_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
              <i class="bi bi-info-circle me-1"></i>Pilih lokasi sewing yang akan menerima dan mengerjakan order ini.
              @if($sewingLocations->isEmpty())
              <strong class="text-warning"> Belum ada data tempat sewing. <a href="{{ route('master.sewing-location.create') }}">Tambah sekarang</a></strong>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Kondisi Barang</label>
          <input type="text" name="condition_notes" class="form-control" value="{{ old('condition_notes') }}" placeholder="Deskripsi kondisi barang saat dikirim...">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Catatan Handover</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
        </div>
      </div>

      <hr>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Item SKU yang Dikirim</h6>
        <button type="button" id="addItem" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus me-1"></i>Tambah SKU</button>
      </div>
      <div id="itemsContainer">
        <div class="row g-2 align-items-end mb-2 item-row">
          <div class="col-md-8">
            <select name="items[0][sku_id]" class="form-select form-select-sm sku-sel"><option value="">-- Pilih SKU --</option></select>
          </div>
          <div class="col-md-3">
            <input type="number" name="items[0][qty_sent]" class="form-control form-control-sm" placeholder="Qty" min="1">
          </div>
          <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 remove-item"><i class="bi bi-trash"></i></button></div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-send me-2"></i>Kirim Handover</button>
        <a href="{{ route('handover.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
// Station sequence data
const stations = {
  @foreach($stations as $st)
  {{ $st->id }}: { seq: {{ $st->order_sequence }}, name: '{{ $st->name }}' },
  @endforeach
};

// SKU loader
let idx = 1;
function loadSKUs(sel) {
  const oid = document.getElementById('orderSel').value;
  if (!oid) { sel.innerHTML = '<option value="">-- Pilih Order dulu --</option>'; return; }
  fetch(`/api/order-skus/${oid}`).then(r => r.json()).then(data => {
    sel.innerHTML = '<option value="">-- Pilih SKU --</option>';
    data.forEach(item => sel.innerHTML += `<option value="${item.sku_id}">${item.sku?.sku_code || ''}</option>`);
  });
}

// Show/hide sewing location based on selected station
function checkSewingLocation() {
  const fromId = parseInt(document.getElementById('stationSel').value);
  const wrapper = document.getElementById('sewingLocationWrapper');
  const sel = document.getElementById('sewingLocationSel');
  const info = document.getElementById('toStationInfo');

  if (!fromId || !stations[fromId]) {
    wrapper.style.display = 'none';
    sel.removeAttribute('required');
    info.style.display = 'none';
    return;
  }

  const fromSeq = stations[fromId].seq;
  // Cari stasiun tujuan (seq + 1)
  const toStation = Object.values(stations).find(s => s.seq === fromSeq + 1);
  if (toStation) {
    info.style.display = 'block';
    info.textContent = '→ Tujuan: ' + toStation.name;
  } else {
    info.style.display = 'none';
  }

  // Tampilkan field sewing location jika tujuan adalah Sewing (seq=2) atau asal adalah Sewing (seq=2)
  const toSeq = fromSeq + 1;
  const needsSewingLocation = toSeq === 2 || fromSeq === 2;

  if (needsSewingLocation) {
    wrapper.style.display = 'block';
    sel.setAttribute('required', 'required');
  } else {
    wrapper.style.display = 'none';
    sel.removeAttribute('required');
    sel.value = '';
  }
}

document.getElementById('stationSel').addEventListener('change', checkSewingLocation);
document.getElementById('orderSel').addEventListener('change', function () {
  document.querySelectorAll('.sku-sel').forEach(s => loadSKUs(s));
});

// Init
checkSewingLocation();
loadSKUs(document.querySelector('.sku-sel'));

// Re-check if old value was set (after validation error)
@if(old('from_station_id'))
document.getElementById('stationSel').value = '{{ old("from_station_id") }}';
checkSewingLocation();
@endif

// Add item row
document.getElementById('addItem').addEventListener('click', function () {
  const div = document.createElement('div');
  div.className = 'row g-2 align-items-end mb-2 item-row';
  div.innerHTML = `<div class="col-md-8"><select name="items[${idx}][sku_id]" class="form-select form-select-sm sku-sel"><option value="">-- Pilih SKU --</option></select></div><div class="col-md-3"><input type="number" name="items[${idx}][qty_sent]" class="form-control form-control-sm" placeholder="Qty" min="1"></div><div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 remove-item"><i class="bi bi-trash"></i></button></div>`;
  document.getElementById('itemsContainer').appendChild(div);
  loadSKUs(div.querySelector('.sku-sel'));
  div.querySelector('.remove-item').addEventListener('click', () => div.remove());
  idx++;
});
document.querySelectorAll('.remove-item').forEach(b => b.addEventListener('click', () => b.closest('.item-row').remove()));
</script>
@endpush
