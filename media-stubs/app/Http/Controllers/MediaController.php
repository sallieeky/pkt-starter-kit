<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Media $media)
    {
        return response()->file(storage_path('app/public/' . $media->path));
    }
}
