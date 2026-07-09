@props(['skipSuccess' => false])
@if(session('success') && !$skipSuccess)
    <div class="alert alert-success alert-dismissible fade show d-flex gap-2 align-items-start" role="alert">
        <i class="bi bi-check-circle-fill mt-1"></i>
        <div>
            {{ session('success') }}
            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="mt-2">
                    <strong>Peringatan baris yang dilewati:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach(session('import_errors') as $err)
                            <li class="small">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@elseif(session('import_errors') && count(session('import_errors')) > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Beberapa baris dilewati:</strong>
        <ul class="mb-0 mt-1">
            @foreach(session('import_errors') as $err)
                <li class="small">{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
