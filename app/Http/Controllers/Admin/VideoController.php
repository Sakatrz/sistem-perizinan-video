<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->paginate(10);

        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.form', ['video' => new Video()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:512000',
        ]);

        $file = $request->file('video');
        $path = $file->store('videos', 'local');

        Video::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil diupload.');
    }

    public function edit(Video $video)
    {
        return view('admin.videos.form', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:512000',
        ]);

        $video->title = $data['title'];
        $video->description = $data['description'] ?? null;

        if ($request->hasFile('video')) {
            Storage::disk('local')->delete($video->file_path);
            $file = $request->file('video');
            $video->file_path = $file->store('videos', 'local');
            $video->mime_type = $file->getClientMimeType();
        }
        $video->save();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(Video $video)
    {
        Storage::disk('local')->delete($video->file_path);
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil dihapus.');
    }
}
