<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\VideoAccess;
use Illuminate\Http\Request;

class AccessRequestController extends Controller
{
    public function index()
    {
        $requests = AccessRequest::with(['user', 'video', 'access'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(15);

        return view('admin.requests.index', compact('requests'));
    }

    /** Setujui permintaan (pertama kali) dengan durasi dihitung dari sekarang. */
    public function approve(Request $request, AccessRequest $accessRequest)
    {
        if ($accessRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }
        $end = $this->grant($request, $accessRequest);

        return back()->with('success', 'Akses disetujui sampai '.$end->format('d M Y H:i').' WIB.');
    }

    /** Ubah durasi akses yang sudah disetujui (dihitung ulang dari sekarang). */
    public function updateAccess(Request $request, AccessRequest $accessRequest)
    {
        abort_unless($accessRequest->status === 'approved', 404);
        $end = $this->grant($request, $accessRequest);

        return back()->with('success', 'Durasi akses diubah, berlaku sampai '.$end->format('d M Y H:i').' WIB.');
    }

    /** Cabut akses sekarang juga. */
    public function revoke(AccessRequest $accessRequest)
    {
        abort_unless($accessRequest->status === 'approved', 404);
        $accessRequest->access?->update(['expires_at' => now()]);

        return back()->with('success', 'Akses telah dicabut.');
    }

    public function reject(AccessRequest $accessRequest)
    {
        if ($accessRequest->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan pending yang bisa ditolak.');
        }
        $accessRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Permintaan ditolak.');
    }

    private function grant(Request $request, AccessRequest $accessRequest)
    {
        $data = $request->validate([
            'duration' => 'required|integer|min:1',
            'unit' => 'required|in:minutes,hours,days',
        ]);

        // Batas wajar per satuan (mencegah salah ketik, maks 1 tahun)
        $max = ['minutes' => 525600, 'hours' => 8760, 'days' => 365][$data['unit']];
        if ((int) $data['duration'] > $max) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'duration' => "Durasi maksimal {$max} {$data['unit']} (1 tahun).",
            ]);
        }

        $duration = (int) $data['duration'];
        $start = now();
        $end = match ($data['unit']) {
            'minutes' => $start->copy()->addMinutes($duration),
            'hours' => $start->copy()->addHours($duration),
            'days' => $start->copy()->addDays($duration),
        };

        $accessRequest->update(['status' => 'approved']);

        VideoAccess::updateOrCreate(
            ['access_request_id' => $accessRequest->id],
            [
                'user_id' => $accessRequest->user_id,
                'video_id' => $accessRequest->video_id,
                'starts_at' => $start,
                'expires_at' => $end,
            ]
        );

        return $end;
    }
}
