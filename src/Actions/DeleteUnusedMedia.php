<?php

namespace Pkt\StarterKit\Actions;

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteUnusedMedia
{
    public function __invoke()
    {
        DB::beginTransaction();
        try {
            $paths = [];
            $media = Media::query()
                ->leftJoin('mediables', 'mediables.media_id', '=', 'media.id')
                ->whereNull('mediables.media_id')
                ->select('media.*')
                ->get();

            foreach ($media as $m) {
                $paths[] = $m->path;
                $m->delete();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete unuse media',
            ], 500);
        }
        DB::commit();
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
    }
}
