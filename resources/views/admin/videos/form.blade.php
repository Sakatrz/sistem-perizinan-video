@extends('layouts.app')
@section('title', $video->exists ? 'Edit Video' : 'Upload Video')
@section('content')
<h4>{{ $video->exists ? 'Edit' : 'Upload' }} Video</h4>
<form method="POST" enctype="multipart/form-data" class="card card-body mt-3"
      action="{{ $video->exists ? route('admin.videos.update', $video) : route('admin.videos.store') }}">
    @csrf
    @if($video->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label">Judul</label>
        <input name="title" class="form-control" value="{{ old('title', $video->title) }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $video->description) }}</textarea></div>
    <div class="mb-3"><label class="form-label">File Video (mp4/webm/ogg/mov, maks 500MB) {{ $video->exists ? '- kosongkan jika tidak diganti' : '' }}</label>
        <input type="file" name="video" class="form-control" accept="video/*" {{ $video->exists ? '' : 'required' }}></div>
    <div><button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">Batal</a></div>
</form>
@endsection
