<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Pkt\StarterKit\Traits\HasCreatedUpdatedBy;

class Media extends Model
{
    use HasFactory, HasCreatedUpdatedBy, HasUuids;

    /**
     * The attributes that are protected from mass assignment.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'storage_name',
        'path',
    ];

    /**
     * The attributes that are appended to the model.
     *
     * @var array<string, string>
     */
    protected $appends = [
        'url',
        'base64'
    ];

    /**
     * Get the url attribute.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return route('get-media', ['media' => $this->uuid]);
    }

    /**
     * Get the base64 attribute.
     *
     * @return string
     */
    public function getBase64Attribute(): string
    {
        return base64_encode(Storage::disk('public')->get($this->path));
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
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
     * @param  string|null  $collectionName
     * @param  array  $element
     * 
     * @return \App\Models\Media|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public static function createFromElementRequest(?string $collectionName, array $element): Collection
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
     * ======================================================
     * Add your custom methods here
     * ======================================================
     * 
     * Example:
     * 
     * public function users(): MorphToMany
     * {
     *    return $this->morphedByMany(User::class, 'mediable');
     * }
     */
}
