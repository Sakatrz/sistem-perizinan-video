@extends('layouts.app')
@section('title', $video->title)
@section('content')
<a href="{{ route('customer.videos.index') }}" class="btn btn-sm btn-secondary mb-3">&larr; Kembali</a>
<h4>{{ $video->title }}</h4>
<p class="text-muted">{{ $video->description }}</p>
<p>Akses berakhir: <strong>{{ $access->expires_at->format('d M Y H:i:s') }} WIB</strong>
   (<span id="countdown"></span>)</p>
<video id="player" controls controlsList="nodownload" playsinline class="w-100 bg-black rounded" style="max-height:70vh">
    <source src="{{ route('customer.videos.stream', $video) }}" type="{{ $video->mime_type ?? 'video/mp4' }}">
</video>
<script>
    const expires = new Date("{{ $access->expires_at->toIso8601String() }}").getTime();
    const el = document.getElementById('countdown');
    const timer = setInterval(() => {
        const left = expires - Date.now();
        if (left <= 0) {
            clearInterval(timer);
            document.getElementById('player').pause();
            alert('Waktu akses habis. Silakan request akses ulang.');
            location.href = "{{ route('customer.videos.index') }}";
            return;
        }
        const h = Math.floor(left / 3600000), m = Math.floor(left % 3600000 / 60000), s = Math.floor(left % 60000 / 1000);
        el.textContent = `${h}j ${m}m ${s}d tersisa`;
    }, 1000);
</script>
@endsection
