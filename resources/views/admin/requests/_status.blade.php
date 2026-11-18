@if($r->status === 'pending')<span class="badge bg-warning text-dark">Pending</span>
@elseif($active)<span class="badge bg-success">Aktif</span>
@elseif($expired)<span class="badge bg-secondary">Kedaluwarsa</span>
@else<span class="badge bg-danger">Ditolak</span>@endif
