<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,ogg,qt,pdf|max:20480',
            'type' => 'required|string|in:image,video,pdf'
        ]);

        $file = $request->file('file');
        $type = $request->input('type');

        $path = $file->store("media/{$type}", 'public');
        $url = Storage::url($path);

        return response()->json([
            'url' => $url,
            'path' => $path,
            'message' => 'Media berhasil diupload'
        ]);
    }
}
