@extends('layouts.app')
@section('title', 'Data Customer')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Data Customer</h4>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">+ Tambah</a>
</div>

{{-- TABEL: >= 768px --}}
<div class="d-none d-md-block">
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th width="50">#</th><th>Nama</th><th>Email</th><th>Telepon</th><th width="170" class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($customers as $c)
                    <tr>
                        <td>{{ $loop->iteration + ($customers->currentPage()-1) * $customers->perPage() }}</td>
                        <td class="fw-semibold">{{ $c->name }}</td>
                        <td>{{ $c->email }}</td>
                        <td>{{ $c->phone ?: '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.customers.edit', $c) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form method="POST" action="{{ route('admin.customers.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Hapus customer ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada customer.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- KARTU: < 768px --}}
<div class="d-md-none">
@forelse($customers as $c)
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="fw-semibold">{{ $c->name }}</div>
            <div class="small text-muted text-break">{{ $c->email }}</div>
            <div class="small text-muted">Telp: {{ $c->phone ?: '-' }}</div>
            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('admin.customers.edit', $c) }}" class="btn btn-sm btn-warning flex-fill">Edit</a>
                <form method="POST" action="{{ route('admin.customers.destroy', $c) }}" class="flex-fill" onsubmit="return confirm('Hapus customer ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger w-100">Hapus</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info">Belum ada customer.</div>
@endforelse
</div>

{{ $customers->links('pagination::bootstrap-5') }}
@endsection
