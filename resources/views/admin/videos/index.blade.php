@extends('layouts.app')
@section('title', 'Data Video')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Data Video</h4>
    <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">+ Upload</a>
</div>

{{-- TABEL: >= 768px --}}
<div class="d-none d-md-block">
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th width="50">#</th><th>Judul</th><th>Deskripsi</th><th>File</th><th width="170" class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($videos as $v)
                    <tr>
                        <td>{{ $loop->iteration + ($videos->currentPage()-1) * $videos->perPage() }}</td>
                        <td class="fw-semibold">{{ $v->title }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($v->description, 80) ?: '-' }}</td>
                        <td><small class="text-muted">{{ basename($v->file_path) }}</small></td>
                        <td class="text-end">
                            <a href="{{ route('admin.videos.edit', $v) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form method="POST" action="{{ route('admin.videos.destroy', $v) }}" class="d-inline" onsubmit="return confirm('Hapus video ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada video.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- KARTU: < 768px --}}
<div class="d-md-none">
@forelse($videos as $v)
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="fw-semibold">{{ $v->title }}</div>
            @if($v->description)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($v->description, 120) }}</div>@endif
            <div class="small text-muted text-break mt-1">File: {{ basename($v->file_path) }}</div>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('admin.videos.edit', $v) }}" class="btn btn-sm btn-warning flex-fill">Edit</a>
                <form method="POST" action="{{ route('admin.videos.destroy', $v) }}" class="flex-fill" onsubmit="return confirm('Hapus video ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger w-100">Hapus</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info">Belum ada video.</div>
@endforelse
</div>

{{ $videos->links('pagination::bootstrap-5') }}
@endsection
