@php
    $pending = $r->status === 'pending';
@endphp
@if($pending || $r->status === 'approved')
    <form method="POST"
          action="{{ $pending ? route('admin.requests.approve', $r) : route('admin.requests.update-access', $r) }}"
          data-confirm-grant>
        @csrf
        <label class="form-label small text-muted mb-1">
            {{ $pending ? 'Durasi akses' : 'Ubah durasi (dihitung dari sekarang)' }}
        </label>
        <div class="input-group input-group-sm">
            <input type="number" name="duration" value="1" min="1" class="form-control" style="min-width:60px" required>
            <select name="unit" class="form-select" style="min-width:90px">
                <option value="minutes">Menit</option>
                <option value="hours">Jam</option>
                <option value="days" selected>Hari</option>
            </select>
            <button class="btn {{ $pending ? 'btn-success' : 'btn-primary' }}">{{ $pending ? 'Setujui' : 'Ubah' }}</button>
        </div>
    </form>
@endif
<div class="d-flex flex-wrap gap-2 mt-2">
    @if($pending)
        <form method="POST" action="{{ route('admin.requests.reject', $r) }}" onsubmit="return confirm('Tolak permintaan ini?')">@csrf
            <button class="btn btn-sm btn-outline-danger">Tolak</button>
        </form>
    @endif
    @if($active)
        <form method="POST" action="{{ route('admin.requests.revoke', $r) }}" onsubmit="return confirm('Cabut akses sekarang? Customer tidak bisa menonton lagi.')">@csrf
            <button class="btn btn-sm btn-outline-danger">Cabut Akses</button>
        </form>
    @endif
</div>
