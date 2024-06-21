<?php

namespace Pkt\StarterKit\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait InteractsWithMedia
{
    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // Check if the method is a dynamic relationship method
        if ($relationship = $this->getDynamicRelationship($method)) {
            return $relationship;
        }

        // Call the parent method if it's not a dynamic relationship call
        return parent::__call($method, $parameters);
    }

    /**
     * Attempt to get a dynamic relationship based on the method name.
     *
     * @param string $method
     * @return \Illuminate\Database\Eloquent\Relations\Relation|null
     */
    protected function getDynamicRelationship($method)
    {
        if (preg_match('/^media(.+)$/', $method, $matches)) {
            $collectionName = [
                Str::snake($matches[1]),
                Str::kebab($matches[1]),
                Str::camel($matches[1]),
                Str::studly($matches[1]),
                Str::slug($matches[1]),
                Str::title($matches[1]),
                Str::ucfirst($matches[1]),
                Str::headline($matches[1]),
                Str::lower(Str::headline($matches[1])),
            ];
            $collectionName = array_unique($collectionName);

            return $this->morphToMany(Media::class, 'mediable')
                ->wherePivotIn('collection_name', $collectionName)
                ->withPivot('collection_name');
        }

        return null;
    }

    /**
     * The collection name to be used when attaching media to the model
     * Collection name can be set using the setMediaCollection method
     * Collection name also used to naming the folder where the media will be stored
     * 
     * @var string $collectionName
     */
    private static $collectionName;

    /**
     * Get all media associated with the model instance
     * 
     * @return MorphToMany
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')->withPivot('collection_name');
    }

    /**
     * Set the collection name to be used when attaching media to the model
     * 
     * @param string $collectionName 
     * 
     * @return self
     */
    public function setMediaCollection(string $collectionName): self
    {
        self::$collectionName = $collectionName;
        return $this;
    }

    /**
     * Get all media associated with the model instance
     * 
     * @return Collection<Media>
     */
    public function getAllMedia(): Collection
    {
        return $this->media()->get();
    }

    /**
     * Get media from a specific collection associated with the model instance
     * 
     * @param string|null $collectionName
     * 
     * @return Collection<Media>
     */
    public function getMediaFromCollection(?string $collectionName): Collection
    {
        $collectionName = $collectionName ?? self::$collectionName;
        return $this->media()->wherePivot('collection_name', $collectionName)->get();
    }

    /**
     * Get first media from a specific collection associated with the model instance
     * 
     * @param string|null $collectionName
     * 
     * @return Media
     */
    public function getFirstMediaFromCollection(?string $collectionName): Media
    {
        $collectionName = $collectionName ?? self::$collectionName;
        return $this->media()->wherePivot('collection_name', $collectionName)->latest()->first();
    }

    /**
     * Get accepted media collections from the model instance
     * 
     * @return array
     */
    public function getAcceptedMediaCollections(): array
    {
        return (new self)->acceptedMediaCollections ?? ["*"];
    }

    /**
     * Get available media collections from the model instance
     * 
     * @return array
     */
    public static function getAvailableMediaCollections(): array
    {
        return (new self)
            ->with('media')
            ->get()
            ?->pluck('media')
            ?->flatten()
            ?->pluck('pivot.collection_name')
            ?->unique()
            ?->toArray();
    }

    /**
     * Attach media from an Element Plus request to the model instance
     * The media will be stored in the public disk
     * The media will be stored in the collection name folder
     * The media will be attached to the model instance
     * 
     * @param array $media
     * @param string|null $collectionName
     * 
     * @return self
     */
    public function attachMediaFromElementRequest(array $media, ?string $collectionName = null): self
    {
        $collectionName = $collectionName ?? self::$collectionName;
        DB::beginTransaction();
        try {
            $storedMedia = [];

            if (!in_array($collectionName, $this->getAcceptedMediaCollections()) && !in_array('*', $this->getAcceptedMediaCollections())){
                throw new \Exception('Collection ' . $collectionName . ' not accepted');
            }

            foreach ($media as $item) {
                $item = $item['raw'];
                $storageName = $item->hashName();
                $path = $item->storeAs($collectionName, $storageName, 'public');
                $storedMedia[] = $path;
                $item = Media::create([
                    'original_name' => $item->getClientOriginalName(),
                    'storage_name' => $storageName,
                    'path' => $path,
                    'type' => $item->getType(),
                    'size' => $item->getSize(),
                    'extension' => $item->getExtension(),
                    'mime_type' => $item->getMimeType(),
                ]);
                $this->media()->attach($item->id, ['collection_name' => $collectionName]);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($storedMedia as $path) {
                Storage::disk('public')->delete($path);
            }
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

    /**
     * Attach media from an existing media to the model instance
     * The media will be attached to the model instance
     * 
     * @param Media|Collection|array|int $media
     * @param string|null $collectionName
     * 
     * @return self
     */
    public function attachMediaFromExisting(Media|Collection|array|int $media, ?string $collectionName = null): self
    {
        $collectionName = $collectionName ?? self::$collectionName;
        DB::beginTransaction();
        try {
            if (!in_array($collectionName, $this->getAcceptedMediaCollections()) && !in_array('*', $this->getAcceptedMediaCollections())){
                throw new \Exception('Collection ' . $collectionName . ' not accepted');
            }

            if (is_array($media) || $media instanceof Collection) {
                foreach ($media as $item) {
                    if ($item instanceof Media) {
                        $this->media()->attach($item->id, ['collection_name' => $collectionName]);
                    } else {
                        $this->media()->attach($item, ['collection_name' => $collectionName]);
                    }
                }
            } else if ($media instanceof Media) {
                $this->media()->attach($media->id, ['collection_name' => $collectionName]);
            } else if (is_int($media)) {
                $this->media()->attach($media, ['collection_name' => $collectionName]);
            } else {
                throw new \Exception('Invalid type');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

    /**
     * Attach media from an uploaded file to the model instance
     * The media will be stored in the public disk
     * The media will be stored in the collection name folder
     * The media will be attached to the model instance
     * 
     * @param UploadedFile|array $file
     * @param string|null $collectionName
     * 
     * @return self
     */
    public function attachMediaFromUploadedFile(UploadedFile|array $file, ?string $collectionName = null): self
    {
        $collectionName = $collectionName ?? self::$collectionName;
        DB::beginTransaction();
        try {
            $storedMedia = [];

            if (!in_array($collectionName, $this->getAcceptedMediaCollections()) && !in_array('*', $this->getAcceptedMediaCollections())){
                throw new \Exception('Collection ' . $collectionName . ' not accepted');
            }

            if ($file instanceof UploadedFile) {
                $storageName = $file->hashName();
                $path = $file->storeAs($collectionName, $storageName, 'public');
                $storedMedia[] = $path;
                $media = Media::create([
                    'original_name' => $file->getClientOriginalName(),
                    'storage_name' => $storageName,
                    'path' => $path,
                    'type' => $file->getType(),
                    'size' => $file->getSize(),
                    'extension' => $file->getExtension(),
                    'mime_type' => $file->getMimeType(),
                ]);
                $this->media()->attach($media->id, ['collection_name' => $collectionName]);
            } else if (is_array($file)) {
                foreach ($file as $item) {
                    if ($item instanceof UploadedFile) {
                        $storageName = $item->hashName();
                        $path = $item->storeAs($collectionName, $storageName, 'public');
                        $storedMedia[] = $path;
                        $item = Media::create([
                            'original_name' => $item->getClientOriginalName(),
                            'storage_name' => $storageName,
                            'path' => $path,
                            'type' => $item->getType(),
                            'size' => $item->getSize(),
                            'extension' => $item->getExtension(),
                            'mime_type' => $item->getMimeType(),
                        ]);
                        $this->media()->attach($item->id, ['collection_name' => $collectionName]);
                    } else {
                        throw new \Exception('Invalid file type');
                    }
                }
            } else {
                throw new \Exception('Invalid type');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($storedMedia as $storedMedia) {
                Storage::disk('public')->delete($storedMedia);
            }
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

    /**
     * Detach media from the model instance
     * 
     * @param Media|Collection|array|int $media
     * 
     * @return self
     */
    public function detachMedia(Media|Collection|array|int $media): self
    {
        DB::beginTransaction();
        try {
            if (is_array($media) || $media instanceof Collection) {
                foreach ($media as $item) {
                    if ($item instanceof Media) {
                        $this->media()->detach($item->id);
                    } else {
                        $this->media()->detach($item);
                    }
                }
            } else if ($media instanceof Media) {
                $this->media()->detach($media->id);
            } else if (is_int($media)) {
                $this->media()->detach($media);
            } else {
                throw new \Exception('Invalid type');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

    /**
     * Detach media from a specific collection associated with the model instance
     * 
     * @param string|null $collectionName
     * 
     * @return self
     */
    public function detachMediaFromCollection(?string $collectionName): self
    {
        $collectionName = $collectionName ?? self::$collectionName;

        if (!in_array($collectionName, $this->getAvailableMediaCollections())) {
            throw new \Exception('Collection name not available');
        }

        DB::beginTransaction();
        try {
            $this->media()->wherePivot('collection_name', $collectionName)->detach();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

    /**
     * Detach all media associated with the model instance
     * 
     * @return self
     */
    public function detachAllMedia(): self
    {
        DB::beginTransaction();
        try {
            $this->media()->detach();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

    /**
     * Sync media to the model instance
     * 
     * @param array|SupportCollection|Collection|Media|int $media
     * @param string|null $collectionName
     * 
     * @return self
     */
    public function syncMedia(array|SupportCollection|Collection|Media|int $media, ?string $collectionName = null): self
    {
        $collectionName = $collectionName ?? self::$collectionName;
        DB::beginTransaction();
        try {
            if (!in_array($collectionName, $this->getAcceptedMediaCollections()) && !in_array('*', $this->getAcceptedMediaCollections())){
                throw new \Exception('Collection ' . $collectionName . ' not accepted');
            }

            if ($media instanceof Collection) {
                $media = $media?->pluck('id');
            } else if ((is_array($media) || $media instanceof SupportCollection) && !array_key_exists('id', $media->toArray())) {
                $media = $media;
            } else if ($media instanceof Media) {
                $media = collect($media->id);
            } else if (is_int($media)) {
                $media = collect($media);
            } else {
                throw new \Exception('Invalid type');
            }

            $this->media()
                ->wherePivot('collection_name', $collectionName)
                ->syncWithPivotValues($media, ['collection_name' => $collectionName]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

    /**
     * Sync media to the model instance from an Element Plus request
     *
     * @param array $media
     * @param string|null $collectionName
     *
     * @return self
     */
    public function syncMediaFromElementRequest(array $media, ?string $collectionName = null): self
    {
        $collectionName = $collectionName ?? self::$collectionName;
        DB::beginTransaction();
        try {
            $storedMedia = [];

            if (!in_array($collectionName, $this->getAcceptedMediaCollections()) && !in_array('*', $this->getAcceptedMediaCollections())){
                throw new \Exception('Collection ' . $collectionName . ' not accepted');
            }

            $media = collect($media)->map(function ($item) use ($collectionName, &$storedMedia) {
                if (isset($item['raw'])){
                    $item = $item['raw'];
                    $storageName = $item->hashName();
                    $path = $item->storeAs($collectionName, $storageName, 'public');
                    $storedMedia[] = $path;
                    $item = Media::create([
                        'original_name' => $item->getClientOriginalName(),
                        'storage_name' => $storageName,
                        'path' => $path,
                        'type' => $item->getType(),
                        'size' => $item->getSize(),
                        'extension' => $item->getExtension(),
                        'mime_type' => $item->getMimeType(),
                    ]);
                    return (int) $item->id;
                } else {
                    return (int) $item['id'];
                }
            });

            $this->media()
                ->wherePivot('collection_name', $collectionName)
                ->syncWithPivotValues($media, ['collection_name' => $collectionName]);
        } catch (\Throwable $e) {
            DB::rollBack();
            foreach ($storedMedia as $path) {
                Storage::disk('public')->delete($path);
            }
            throw new \Exception($e->getMessage());
        }
        DB::commit();

        return $this;
    }

}
