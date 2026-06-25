@props(['importRoute', 'templateRoute', 'label' => 'Data'])

@php
    $modalId = 'importModal_' . preg_replace('/[^a-z0-9]/i', '_', $label) . '_' . substr(md5($importRoute), 0, 6);
@endphp

<div class="d-inline-flex gap-2 align-items-center">
    <a href="{{ $templateRoute }}" class="btn btn-outline-success btn-sm">
        <i class="bi bi-file-earmark-arrow-down me-1"></i>Template {{ $label }}
    </a>
    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
        <i class="bi bi-upload me-1"></i>Import Excel
    </button>
</div>

@push('modals')
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}_label" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ $importRoute }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}_label">
                        <i class="bi bi-upload me-2"></i>Import {{ $label }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex gap-2 align-items-start py-2 mb-3">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div class="small">
                            Gunakan file dari tombol <strong>Template {{ $label }}</strong>.
                            Data yang sudah ada (berdasarkan kode) akan diperbarui, data baru akan ditambahkan.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Format: .xlsx, .xls, atau .csv — maks. 5 MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Upload &amp; Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush
