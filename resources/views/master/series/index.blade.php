@extends('layouts.app')
@section('title', 'Master Series')
@section('content')
@php
    $allColors = \App\Models\Color::orderBy('name')->get();
    $allSizes  = \App\Models\Size::orderBy('sort_order')->get();
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Master Series</h4>
    @if(auth()->user()->isAdmin())
    <div class="d-flex gap-2 align-items-center">
        <x-import-button import-route="{{ route('import.series') }}" template-route="{{ route('import.template.series') }}" label="Series" />
        <a href="{{ route('master.series.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Series</a>
    </div>
    @endif
</div>

<x-import-result />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="GET" class="d-flex gap-2 align-items-center mb-3 flex-wrap">
    <div class="input-group input-group-sm" style="max-width:280px">
        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama series..." value="{{ request('search') }}">
    </div>
    <select name="product_id" class="form-select form-select-sm" style="max-width:200px">
        <option value="">Semua Produk</option>
        @foreach($products as $p)
        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>
    @if(request()->anyFilled(['search','product_id']))
    <a href="{{ route('master.series.index') }}" class="btn btn-sm btn-outline-danger">Reset</a>
    @endif
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Series</th>
                    <th>Produk</th>
                    <th>Jumlah SKU</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($series as $s)
                <tr>
                    <td class="fw-semibold text-primary">{{ $s->code }}</td>
                    <td class="fw-semibold">{{ $s->name }}</td>
                    <td>{{ $s->product->name ?? '-' }}</td>
                    <td><span class="badge bg-info">{{ $s->skus->count() }} SKU</span></td>
                    <td>
                        <span class="badge bg-{{ $s->is_active ? 'success' : 'secondary' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-success me-1"
                            data-bs-toggle="modal" data-bs-target="#modal-sku-{{ $s->id }}">
                            <i class="bi bi-magic"></i> Generate SKU
                        </button>
                        <a href="{{ route('master.series.edit', $s) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.series.destroy', $s) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus series {{ $s->name }}?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>

                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-collection"></i><p class="fw-semibold mb-1">Belum ada series</p><p>Tambahkan series pertama</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($series->hasPages())
    <div class="card-footer">{{ $series->links() }}</div>
    @endif
</div>

{{-- Modals Generate SKU (di luar table agar DOM valid) --}}
@if(auth()->user()->isAdmin())
@foreach($series as $s)
<div class="modal fade" id="modal-sku-{{ $s->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('master.series.generate-sku', $s) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-magic me-2 text-success"></i>Generate SKU — {{ $s->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Pilih warna dan ukuran. Sistem akan membuat semua kombinasi SKU yang belum ada.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warna</label>
                            <div class="border rounded p-2" style="max-height:220px;overflow-y:auto">
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox"
                                        onchange="this.closest('.border').querySelectorAll('[name]').forEach(c=>c.checked=this.checked)">
                                    <label class="form-check-label fw-semibold text-primary">Pilih Semua</label>
                                </div>
                                <hr class="my-1">
                                @foreach($allColors as $c)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="color_ids[]" value="{{ $c->id }}" id="col-{{ $s->id }}-{{ $c->id }}">
                                    <label class="form-check-label" for="col-{{ $s->id }}-{{ $c->id }}">
                                        @if($c->hex_code)
                                        <span class="d-inline-block rounded-circle border me-1" style="width:12px;height:12px;background:#{{ $c->hex_code }}"></span>
                                        @endif
                                        {{ $c->name }} <small class="text-muted">({{ $c->code }})</small>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ukuran</label>
                            <div class="border rounded p-2" style="max-height:220px;overflow-y:auto">
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox"
                                        onchange="this.closest('.border').querySelectorAll('[name]').forEach(c=>c.checked=this.checked)">
                                    <label class="form-check-label fw-semibold text-primary">Pilih Semua</label>
                                </div>
                                <hr class="my-1">
                                @foreach($allSizes as $sz)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="size_ids[]" value="{{ $sz->id }}" id="sz-{{ $s->id }}-{{ $sz->id }}">
                                    <label class="form-check-label" for="sz-{{ $s->id }}-{{ $sz->id }}">{{ $sz->name }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>SKU yang sudah ada tidak akan diduplikasi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-magic me-1"></i>Generate SKU</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

@endsection
