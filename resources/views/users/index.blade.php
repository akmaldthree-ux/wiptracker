@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Manajemen Pengguna</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna</a>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Stasiun</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width:36px;height:36px;background:var(--primary);font-size:.85rem;flex-shrink:0">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                @if($user->id === auth()->id())<small class="text-muted">(Anda)</small>@endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php
                            $roleColors = ['admin'=>'danger','supervisor'=>'primary','pic_stasiun'=>'info','manager'=>'purple','staff_gudang'=>'success'];
                            $rc = $roleColors[$user->role] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $rc }}">{{ $user->getRoleLabelAttribute() }}</span>
                    </td>
                    <td>{{ $user->station->name ?? '-' }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-end">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan pengguna ini?')"><i class="bi bi-person-x"></i></button>
                        </form>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i><p class="fw-semibold mb-1">Belum ada pengguna</p><p>Tambahkan pengguna untuk mulai menggunakan sistem</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
