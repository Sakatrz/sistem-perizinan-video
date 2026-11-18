@extends('layouts.app')
@section('title', 'Permintaan Akses')
@section('content')
<h4 class="mb-3">Permintaan Akses Video</h4>

@php
    $state = function ($r) {
        $expired = $r->status === 'approved' && $r->access && $r->access->expires_at->isPast();
        return [$expired, $r->status === 'approved' && ! $expired];
    };
@endphp

{{-- TABEL: >= 1024px --}}
<div class="d-none d-lg-block">
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th><th>Video</th><th>Diminta</th><th>Status</th><th>Berlaku s/d</th><th style="min-width:260px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $r)
                    @php([$expired, $active] = $state($r))
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $r->user->name }}</div>
                            <div class="small text-muted">{{ $r->user->email }}</div>
                        </td>
                        <td>{{ $r->video->title }}</td>
                        <td class="small">{{ $r->created_at->format('d M Y') }}<br>{{ $r->created_at->format('H:i') }} WIB</td>
                        <td>@include('admin.requests._status')</td>
                        <td class="small {{ $active ? 'text-success' : 'text-muted' }}">
                            @if($r->access){{ $r->access->expires_at->format('d M Y') }}<br>{{ $r->access->expires_at->format('H:i') }} WIB @else - @endif
                        </td>
                        <td>@include('admin.requests._actions')</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada permintaan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- KARTU: < 1024px --}}
<div class="d-lg-none">
@forelse($requests as $r)
    @php([$expired, $active] = $state($r))
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="min-w-0">
                    <div class="fw-semibold">{{ $r->user->name }}</div>
                    <div class="small text-muted text-break">{{ $r->user->email }}</div>
                </div>
                @include('admin.requests._status')
            </div>
            <hr class="my-2">
            <div>Video: <strong>{{ $r->video->title }}</strong></div>
            <div class="small text-muted">Diminta: {{ $r->created_at->format('d M Y H:i') }} WIB</div>
            @if($r->access)
                <div class="small {{ $active ? 'text-success' : 'text-muted' }}">Berlaku s/d: {{ $r->access->expires_at->format('d M Y H:i') }} WIB</div>
            @endif
            @if($r->status !== 'rejected')
                <div class="mt-3">@include('admin.requests._actions')</div>
            @endif
        </div>
    </div>
@empty
    <div class="alert alert-info">Belum ada permintaan.</div>
@endforelse
</div>

{{ $requests->links('pagination::bootstrap-5') }}
@endsection

@push('scripts')
<script>
    const unitLabel = { minutes: 'menit', hours: 'jam', days: 'hari' };
    document.querySelectorAll('form[data-confirm-grant]').forEach(f => {
        f.addEventListener('submit', e => {
            const d = f.querySelector('[name=duration]').value;
            const u = unitLabel[f.querySelector('[name=unit]').value];
            if (!confirm(`Berikan akses selama ${d} ${u}?\nPastikan satuan waktu sudah benar.`)) e.preventDefault();
        });
    });
</script>
@endpush
