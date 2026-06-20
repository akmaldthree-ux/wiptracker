@extends('layouts.app')
@section('title', $action === 'create' ? 'Tambah Supplier' : 'Edit Supplier')
@section('page-title', $action === 'create' ? 'Tambah Supplier' : 'Edit Supplier')
@section('content')

<div class="card" style="max-width:640px">
  <div class="card-header">
    <i class="bi bi-truck me-2"></i>{{ $action === 'create' ? 'Tambah Supplier Baru' : 'Edit Supplier' }}
  </div>
  <div class="card-body">
    @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ $action === 'create' ? route('master.supplier.store') : route('master.supplier.update', $supplier) }}">
      @csrf
      @if($action === 'edit') @method('PUT') @endif

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Kode Supplier <span class="text-danger">*</span></label>
          <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                 value="{{ old('code', $supplier->code) }}" placeholder="SUP-001" required>
          @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $supplier->name) }}" placeholder="PT. Supplier Jaya" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">No. Telepon</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}" placeholder="08xx-xxxx-xxxx">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}" placeholder="supplier@email.com">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Alamat</label>
          <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap supplier">{{ old('address', $supplier->address) }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">PIC / Contact Person</label>
          <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person) }}" placeholder="Nama PIC">
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">Supplier Aktif</label>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Catatan</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('notes', $supplier->notes) }}</textarea>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
        <a href="{{ route('master.supplier.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
