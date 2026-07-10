@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li><li class="breadcrumb-item active">Tambah</li></ol></nav>
    <h4 class="mb-0 fw-bold">Tambah Pengguna Baru</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span> <small class="text-muted fw-normal">(boleh lebih dari satu)</small></label>
                            @php $selectedRoles = old('roles', []); @endphp
                            @error('roles')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                            <div class="row g-2">
                                @foreach(['admin'=>'Admin','supervisor'=>'Supervisor Produksi','manager'=>'Manager / Owner','pic_stasiun'=>'PIC Stasiun','procurement'=>'Tim Procurement','staff_gudang'=>'Staff Gudang','staff_produksi'=>'Staff Produksi','ie'=>'Industrial Engineering','ppic'=>'PPIC'] as $val=>$label)
                                <div class="col-md-4">
                                    <div class="form-check border rounded px-3 py-2">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $val }}"
                                            id="role_{{ $val }}" onchange="toggleStation()"
                                            {{ in_array($val, $selectedRoles) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="role_{{ $val }}">{{ $label }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6" id="stationField">
                            <label class="form-label fw-semibold">Stasiun</label>
                            <select name="station_id" class="form-select">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($stations as $s)
                                <option value="{{ $s->id }}" {{ old('station_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label fw-semibold" for="is_active">Akun Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStation() {
    const picChecked = document.getElementById('role_pic_stasiun')?.checked;
    document.getElementById('stationField').style.display = picChecked ? '' : 'none';
}
toggleStation();
</script>
@endsection
