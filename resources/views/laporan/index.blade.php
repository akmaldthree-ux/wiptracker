@extends('layouts.app')
@section('title','Laporan')
@section('page-title','Pusat Laporan')
@section('breadcrumb')
<li class="breadcrumb-item active">Laporan</li>
@endsection
@section('content')
<div class="row g-4">
  <div class="col-12">
    <p class="text-muted">Pilih jenis laporan yang ingin Anda lihat.</p>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('laporan.produksi') }}" class="text-decoration-none">
      <div class="card h-100 border-start border-primary border-4">
        <div class="card-body text-center py-4">
          <i class="bi bi-bar-chart display-4 text-primary mb-3 d-block"></i>
          <h5 class="fw-bold text-primary">Laporan Produksi</h5>
          <p class="text-muted small mb-0">Progress & status order produksi</p>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('laporan.handover') }}" class="text-decoration-none">
      <div class="card h-100 border-start border-success border-4">
        <div class="card-body text-center py-4">
          <i class="bi bi-arrow-left-right display-4 text-success mb-3 d-block"></i>
          <h5 class="fw-bold text-success">Laporan Handover</h5>
          <p class="text-muted small mb-0">Rekap handover antar stasiun</p>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('laporan.bahan-baku') }}" class="text-decoration-none">
      <div class="card h-100 border-start border-warning border-4">
        <div class="card-body text-center py-4">
          <i class="bi bi-box-seam display-4 text-warning mb-3 d-block"></i>
          <h5 class="fw-bold text-warning">Laporan Bahan Baku</h5>
          <p class="text-muted small mb-0">Stok & transaksi bahan baku</p>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('laporan.budget') }}" class="text-decoration-none">
      <div class="card h-100 border-start border-danger border-4">
        <div class="card-body text-center py-4">
          <i class="bi bi-currency-dollar display-4 text-danger mb-3 d-block"></i>
          <h5 class="fw-bold text-danger">Laporan Budget</h5>
          <p class="text-muted small mb-0">Budget vs realisasi biaya</p>
        </div>
      </div>
    </a>
  </div>
</div>
@endsection
