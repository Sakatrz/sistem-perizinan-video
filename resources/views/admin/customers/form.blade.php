@extends('layouts.app')
@section('title', $customer->exists ? 'Edit Customer' : 'Tambah Customer')
@section('content')
<h4>{{ $customer->exists ? 'Edit' : 'Tambah' }} Customer</h4>
<form method="POST" class="card card-body mt-3"
      action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
    @csrf
    @if($customer->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label">Nama</label>
        <input name="name" class="form-control" value="{{ old('name', $customer->name) }}" required></div>
    <div class="mb-3"><label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required></div>
    <div class="mb-3"><label class="form-label">Telepon</label>
        <input name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"></div>
    <div class="mb-3"><label class="form-label">Password {{ $customer->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ $customer->exists ? '' : 'required' }}></div>
    <div><button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Batal</a></div>
</form>
@endsection
