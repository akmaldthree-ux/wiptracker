@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Notifikasi</h4>
    @if($notifications->where('is_read', false)->count() > 0)
    <form action="{{ route('notifications.readAll') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca</button>
    </form>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@php
    $unread = $notifications->where('is_read', false);
    $read = $notifications->where('is_read', true);
@endphp

@if($unread->count() > 0)
<h6 class="text-muted mb-3 fw-semibold">BELUM DIBACA ({{ $unread->count() }})</h6>
<div class="card border-0 shadow-sm mb-4">
    @foreach($unread as $n)
    <div class="d-flex align-items-start p-3 border-bottom gap-3" style="background:#fffbf0">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:rgba(232,93,4,0.15)">
            <i class="bi bi-{{ $n->getIconAttribute() }}" style="color:var(--accent)"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-semibold">{{ $n->title }}</div>
            <div class="text-muted small">{{ $n->message }}</div>
            <div class="text-muted" style="font-size:.75rem">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        <form action="{{ route('notifications.read', $n->id) }}" method="POST" class="flex-shrink-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">Tandai Dibaca</button>
        </form>
    </div>
    @endforeach
</div>
@endif

@if($read->count() > 0)
<h6 class="text-muted mb-3 fw-semibold">SUDAH DIBACA</h6>
<div class="card border-0 shadow-sm">
    @foreach($read as $n)
    <div class="d-flex align-items-start p-3 border-bottom gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:#f0f0f0">
            <i class="bi bi-{{ $n->getIconAttribute() }} text-muted"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-semibold text-muted">{{ $n->title }}</div>
            <div class="text-muted small">{{ $n->message }}</div>
            <div class="text-muted" style="font-size:.75rem">{{ $n->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

@if($notifications->isEmpty())
<div class="text-center py-5 text-muted">
    <i class="bi bi-bell-slash fs-1 mb-3 d-block"></i>
    <p>Tidak ada notifikasi</p>
</div>
@endif
@endsection
