@props(['placeholder' => 'Cari...', 'extras' => []])
<form method="GET" class="d-flex gap-2 align-items-center mb-3">
    <div class="input-group input-group-sm" style="max-width:320px">
        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="search" class="form-control" placeholder="{{ $placeholder }}" value="{{ request('search') }}">
        @foreach($extras as $key => $val)
        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endforeach
    </div>
    <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>
    @if(request()->anyFilled(['search', ...array_keys($extras)]))
    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-danger">Reset</a>
    @endif
</form>
