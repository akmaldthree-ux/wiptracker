@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    <h4 class="mb-0 fw-bold">Edit Pengguna: {{ $user->name }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-12"><hr class="my-1"><p class="small text-muted mb-0">Kosongkan password jika tidak ingin mengubahnya.</p></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required id="roleSelect" onchange="toggleStation()">
                                <option value="admin" {{ old('role',$user->role)=='admin'?'selected':'' }}>Admin</option>
                                <option value="supervisor" {{ old('role',$user->role)=='supervisor'?'selected':'' }}>Supervisor Produksi</option>
                                <option value="pic_stasiun" {{ old('role',$user->role)=='pic_stasiun'?'selected':'' }}>PIC Stasiun</option>
                                <option value="manager" {{ old('role',$user->role)=='manager'?'selected':'' }}>Manager / Owner</option>
                                <option value="staff_gudang" {{ old('role',$user->role)=='staff_gudang'?'selected':'' }}>Staff Gudang</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6" id="stationField">
                            <label class="form-label fw-semibold">Stasiun</label>
                            <select name="station_id" class="form-select">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($stations as $s)
                                <option value="{{ $s->id }}" {{ old('station_id',$user->station_id)==$s->id?'selected':'' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Akun Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStation() {
    const role = document.getElementById('roleSelect').value;
    document.getElementById('stationField').style.display = role === 'pic_stasiun' ? '' : 'none';
}
toggleStation();
</script>
@endsection
