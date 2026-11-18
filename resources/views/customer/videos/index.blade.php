@extends('layouts.app')
@section('title', 'Daftar Video')
@section('content')
<h4 class="mb-3">Daftar Video</h4>
<div class="row">
@forelse($videos as $v)
    @php($s = $statuses[$v->id])
    <div class="col-12 col-sm-6 col-lg-4 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5>{{ $v->title }}</h5>
                <p class="text-muted">{{ $v->description }}</p>
                @if($s['status'] === 'active')
                    <p class="small text-success">Akses sampai {{ $s['expires_at']->format('d M Y H:i') }}</p>
                    <a href="{{ route('customer.videos.watch', $v) }}" class="btn btn-success">Tonton</a>
                @elseif($s['status'] === 'pending')
                    <span class="badge bg-warning text-dark">Menunggu persetujuan admin</span>
                @else
                    @if($s['status'] === 'expired')<p class="small text-danger">Waktu akses habis.</p>@endif
                    @if($s['status'] === 'rejected')<p class="small text-danger">Permintaan sebelumnya ditolak.</p>@endif
                    <form method="POST" action="{{ route('customer.videos.request', $v) }}">@csrf
                        <button class="btn btn-primary">{{ $s['status'] === 'none' ? 'Request Akses' : 'Request Akses Ulang' }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@empty
    <p>Belum ada video.</p>
@endforelse
</div>
@endsection
