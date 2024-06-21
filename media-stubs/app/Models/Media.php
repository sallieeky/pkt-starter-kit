<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;
use Pkt\StarterKit\Traits\HasCreatedUpdatedBy;
use Pkt\StarterKit\Traits\MediaDefaultMethod;

class Media extends Model
{
    use HasFactory, HasCreatedUpdatedBy, HasUuids, MediaDefaultMethod;

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
        'name',
        'url',
    ];

    /**
     * Get the name attribute.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->original_name;
    }

    /**
     * Get the url attribute.
     *
     * @return ?string
     */
    public function getUrlAttribute(): ?string
    {
        // return route('get-media', ['media' => $this->uuid]);
        return null;
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
