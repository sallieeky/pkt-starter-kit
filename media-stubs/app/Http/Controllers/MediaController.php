<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Get media file.
     */
    public function getMedia(Media $media, Request $request)
    {
        return response()->file(storage_path('app/public/' . $media->path));
    }
}
