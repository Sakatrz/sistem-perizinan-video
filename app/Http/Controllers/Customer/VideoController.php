<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Video;
use App\Models\VideoAccess;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $videos = Video::latest()->get();

        // status per video: active | expired | pending | rejected | none
        $statuses = [];
        foreach ($videos as $video) {
            $statuses[$video->id] = $this->statusFor($user->id, $video->id);
        }

        return view('customer.videos.index', compact('videos', 'statuses'));
    }

    public function requestAccess(Video $video)
    {
        $user = auth()->user();
        $state = $this->statusFor($user->id, $video->id);

        if (in_array($state['status'], ['active', 'pending'])) {
            return back()->with('error', 'Anda sudah memiliki akses atau permintaan yang menunggu.');
        }

        AccessRequest::create([
            'user_id' => $user->id,
            'video_id' => $video->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan akses dikirim ke admin.');
    }

    public function watch(Video $video)
    {
        $access = $this->activeAccess(auth()->id(), $video->id);
        abort_unless($access, 403, 'Akses Anda belum disetujui atau sudah habis. Silakan request ulang.');

        return view('customer.videos.watch', compact('video', 'access'));
    }

    public function stream(Video $video)
    {
        abort_unless($this->activeAccess(auth()->id(), $video->id), 403);

        $path = Storage::disk('local')->path($video->file_path);
        abort_unless(file_exists($path), 404);

        return response()->file($path, ['Content-Type' => $video->mime_type ?? 'video/mp4']);
    }

    private function activeAccess(int $userId, int $videoId): ?VideoAccess
    {
        return VideoAccess::where('user_id', $userId)
            ->where('video_id', $videoId)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->latest('expires_at')
            ->first();
    }

    private function statusFor(int $userId, int $videoId): array
    {
        if ($access = $this->activeAccess($userId, $videoId)) {
            return ['status' => 'active', 'expires_at' => $access->expires_at];
        }

        $last = AccessRequest::where('user_id', $userId)->where('video_id', $videoId)->latest('id')->first();

        if (! $last) {
            return ['status' => 'none'];
        }

        if ($last->status === 'approved') {
            return ['status' => 'expired']; // disetujui tapi waktu habis
        }

        return ['status' => $last->status]; // pending | rejected
    }
}
