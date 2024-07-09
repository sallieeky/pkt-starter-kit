<?php

namespace Pkt\StarterKit\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Pkt\StarterKit\Helpers\FileHelper;

trait MediaDefaultMethod
{
    /**
     * Get all collections registered in media.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllCollections(): SupportCollection
    {
        return self::query()
            ->join('mediables', 'mediables.media_id', '=', 'media.id')
            ->select('mediables.collection_name')
            ->distinct()
            ->get()
            ->pluck('collection_name');
    }

    /**
     * Create media from uploaded file.
     *
     * @param  \Illuminate\Http\UploadedFile|array  $file
     * @param  string|null  $collectionName
     * 
     * @return \App\Models\Media|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public static function createFromUploadedFile(UploadedFile|array $file, ?string $collectionName): self|Collection
    {
        DB::beginTransaction();
        try {
            $returnFile = null;
            $storedMedia = [];
            if (is_array($file)) {
                foreach ($file as $item) {
                    if (!$item->isValid()) {
                        throw new \Exception('Invalid file');
                    }

                    $collectionName = $collectionName ?? 'default';
                    $storageName = $item->hashName();
                    $path = $item->storeAs($collectionName, $storageName, 'public');

                    $media = new self();
                    $media->original_name = $item->getClientOriginalName();
                    $media->storage_name = $storageName;
                    $media->path = $path;
                    $media->type = $item->getType();
                    $media->size = $item->getSize();
                    $media->extension = $item->getExtension();
                    $media->mime_type = $item->getMimeType();
                    $media->save();

                    $returnFile[] = $media;
                    $storedMedia[] = $path;
                }

                DB::commit();
                return self::query()->whereIn('id', collect($returnFile)->pluck('id')->toArray())->get();

            } else if ($file instanceof UploadedFile) {
                if (!$file->isValid()) {
                    throw new \Exception('Invalid file');
                }

                $collectionName = $collectionName ?? 'default';
                $storageName = $file->hashName();
                $path = $file->storeAs($collectionName, $storageName, 'public');

                $media = new self();
                $media->original_name = $file->getClientOriginalName();
                $media->storage_name = $storageName;
                $media->path = $path;
                $media->type = $file->getType();
                $media->size = $file->getSize();
                $media->extension = $file->getExtension();
                $media->mime_type = $file->getMimeType();
                $media->save();

                $storedMedia[] = $path;

                DB::commit();
                return $media;

            } else {
                throw new \Exception('Invalid file');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($storedMedia as $path) {
                Storage::disk('public')->delete($path);
            }
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create media from Element Plus request.
     *
     * @param  string|null  $collectionName Collection name
     * @param  array  $element Element Plus request
     * 
     * @return \App\Models\Media|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public static function createFromElementRequest(array $element, ?string $collectionName): Collection
    {
        $collectionName = $collectionName ?? 'default';

        DB::beginTransaction();
        try {
            $storedMedia = [];
            $mediaReturn = collect();
            foreach ($element as $item) {
                $item = $item['raw'];
                $storageName = $item->hashName();
                $path = $item->storeAs($collectionName, $storageName, 'public');
                $storedMedia[] = $path;

                $media = new self();
                $media->original_name = $item->getClientOriginalName();
                $media->storage_name = $storageName;
                $media->path = $path;
                $media->type = $item->getType();
                $media->size = $item->getSize();
                $media->extension = $item->getExtension();
                $media->mime_type = $item->getMimeType();
                $media->save();

                $mediaReturn->push($media);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($storedMedia as $path) {
                Storage::disk('public')->delete($path);
            }
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return self::query()->whereIn('id', $mediaReturn->pluck('id')->toArray())->get();
    }

    /**
     * Create media from base64 file.
     *
     * @param  string|array  $base64
     * @param  string|null  $collectionName
     * 
     * @return \App\Models\Media
     * @throws \Exception
     */
    public static function createFromBase64(string|array $base64, ?string $collectionName): Collection
    {
        DB::beginTransaction();
        try {
            $storedMedia = [];
            $mediaReturn = collect();
            if (is_array($base64)) {
                foreach ($base64 as $item) {
                    $collectionName = $collectionName ?? 'default';
                    $file = FileHelper::fromBase64($item);
                    $storageName = $file->hashName();
                    $path = $file->storeAs($collectionName, $storageName, 'public');
                    $storedMedia[] = $path;

                    $media = new self();
                    $media->original_name = $file->getClientOriginalName();
                    $media->storage_name = $storageName;
                    $media->path = $path;
                    $media->type = $file->getType();
                    $media->size = $file->getSize();
                    $media->extension = $file->getExtension();
                    $media->mime_type = $file->getMimeType();
                    $media->save();

                    $mediaReturn->push($media);
                }
            } else if (is_string($base64)) {
                $collectionName = $collectionName ?? 'default';
                $file = FileHelper::fromBase64($base64);
                $storageName = $file->hashName();
                $path = $file->storeAs($collectionName, $storageName, 'public');
                $storedMedia[] = $path;

                $media = new self();
                $media->original_name = $file->getClientOriginalName();
                $media->storage_name = $storageName;
                $media->path = $path;
                $media->type = $file->getType();
                $media->size = $file->getSize();
                $media->extension = $file->getExtension();
                $media->mime_type = $file->getMimeType();
                $media->save();

                $mediaReturn->push($media);
            } else {
                throw new \Exception('Invalid type');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($storedMedia as $path) {
                Storage::disk('public')->delete($path);
            }
            throw new \Exception($e->getMessage());
        }

        DB::commit();
        return $mediaReturn;
    }
}