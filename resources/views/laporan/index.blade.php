@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Laporan</h4>
        <p class="text-muted mb-0">Pilih jenis laporan yang ingin ditampilkan</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('report.production') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:rgba(26,60,110,0.1)">
                        <i class="bi bi-clipboard2-data fs-2" style="color:var(--primary)"></i>
                    </div>
                    <h5 class="fw-bold">Laporan Produksi</h5>
                    <p class="text-muted small mb-0">Progress order produksi, status, dan realisasi target</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <span class="btn btn-sm" style="background:var(--primary);color:#fff">Lihat Laporan <i class="bi bi-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('report.handover') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:rgba(232,93,4,0.1)">
                        <i class="bi bi-arrow-left-right fs-2" style="color:var(--accent)"></i>
                    </div>
                    <h5 class="fw-bold">Laporan Handover</h5>
                    <p class="text-muted small mb-0">Riwayat perpindahan antar stasiun dan selisih quantity</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <span class="btn btn-sm" style="background:var(--accent);color:#fff">Lihat Laporan <i class="bi bi-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('report.material') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:rgba(25,135,84,0.1)">
                        <i class="bi bi-box-seam fs-2 text-success"></i>
                    </div>
                    <h5 class="fw-bold">Laporan Bahan Baku</h5>
                    <p class="text-muted small mb-0">Stok, penerimaan, dan alokasi bahan baku ke produksi</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <span class="btn btn-sm btn-success">Lihat Laporan <i class="bi bi-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('report.budget') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:rgba(111,66,193,0.1)">
                        <i class="bi bi-cash-stack fs-2 text-purple" style="color:#6f42c1"></i>
                    </div>
                    <h5 class="fw-bold">Laporan Budget</h5>
                    <p class="text-muted small mb-0">Realisasi anggaran dan varians biaya produksi</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <span class="btn btn-sm" style="background:#6f42c1;color:#fff">Lihat Laporan <i class="bi bi-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.hover-card { transition: transform .2s, box-shadow .2s; cursor: pointer; }
.hover-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12) !important; }
</style>
@endsection
