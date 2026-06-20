@extends('layouts.app')
@section('title','Buat Inspeksi QC')
@section('page-title','Buat Inspeksi QC')
@section('content')

<form method="POST" action="{{ route('qc.store') }}" enctype="multipart/form-data">
@csrf
@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
  {{-- Info Utama --}}
  <div class="col-12 col-lg-7">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Inspeksi</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Order Produksi <span class="text-danger">*</span></label>
            <select name="production_order_id" class="form-select @error('production_order_id') is-invalid @enderror" required>
              <option value="">-- Pilih Order --</option>
              @foreach($orders as $o)
              <option value="{{ $o->id }}" {{ old('production_order_id') == $o->id ? 'selected' : '' }}>
                {{ $o->order_no }} — {{ $o->product?->name }}
              </option>
              @endforeach
            </select>
            @error('production_order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Inspector <span class="text-danger">*</span></label>
            <select name="inspector_id" class="form-select @error('inspector_id') is-invalid @enderror" required>
              @foreach($users as $u)
              <option value="{{ $u->id }}" {{ (old('inspector_id', auth()->id()) == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
            @error('inspector_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Tanggal & Waktu Inspeksi <span class="text-danger">*</span></label>
            <input type="datetime-local" name="inspected_at" class="form-control @error('inspected_at') is-invalid @enderror"
                   value="{{ old('inspected_at', now()->format('Y-m-d\TH:i')) }}" required>
            @error('inspected_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Total Diperiksa (pcs) <span class="text-danger">*</span></label>
            <input type="number" name="total_checked" class="form-control @error('total_checked') is-invalid @enderror"
                   value="{{ old('total_checked') }}" min="1" required>
            @error('total_checked')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Total Defect (pcs) <span class="text-danger">*</span></label>
            <input type="number" name="total_defect" class="form-control @error('total_defect') is-invalid @enderror"
                   value="{{ old('total_defect', 0) }}" min="0" required>
            @error('total_defect')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Temuan atau catatan inspeksi...">{{ old('notes') }}</textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Foto Bukti Inspeksi</label>
            <input type="file" name="photo_evidence" id="photoEvidence" class="form-control" accept="image/*" capture="environment">
            <div class="mt-2" id="photoPreviewWrap" style="display:none">
              <img id="photoPreview" src="" style="max-height:160px;border-radius:8px;border:2px solid var(--primary)">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Checklist --}}
  <div class="col-12 col-lg-5">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-list-check me-2"></i>Checklist Kualitas</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0" style="font-size:.83rem">
          <thead>
            <tr>
              <th style="width:45%">Item</th>
              <th class="text-center text-success">OK</th>
              <th class="text-center text-danger">Fail</th>
              <th class="text-center text-muted">N/A</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($checklist as $i => $item)
            <tr>
              <td class="fw-semibold py-2">{{ $item }}</td>
              <td class="text-center">
                <input type="radio" name="checklist[{{ $i }}][result]" value="ok"
                       class="form-check-input" {{ old("checklist.{$i}.result", 'ok') === 'ok' ? 'checked' : '' }}>
              </td>
              <td class="text-center">
                <input type="radio" name="checklist[{{ $i }}][result]" value="fail"
                       class="form-check-input" {{ old("checklist.{$i}.result") === 'fail' ? 'checked' : '' }}>
              </td>
              <td class="text-center">
                <input type="radio" name="checklist[{{ $i }}][result]" value="na"
                       class="form-check-input" {{ old("checklist.{$i}.result") === 'na' ? 'checked' : '' }}>
              </td>
              <td>
                <input type="text" name="checklist[{{ $i }}][notes]" class="form-control form-control-sm"
                       value="{{ old("checklist.{$i}.notes") }}" placeholder="...">
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Inspeksi</button>
      <a href="{{ route('qc.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
  </div>
</div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('photoEvidence').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('photoPreview').src = e.target.result;
    document.getElementById('photoPreviewWrap').style.display = '';
  };
  reader.readAsDataURL(file);
});
</script>
@endpush
