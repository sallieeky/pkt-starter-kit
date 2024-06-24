<?php

namespace Pkt\StarterKit\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait HasCreatedUpdatedBy
 *
 * @package Pkt\StarterKit\Traits
 *
 * @property int $created_by
 * @property int $updated_by
 *
 * @mixin \Eloquent
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasCreatedUpdatedBy
{
    /**
     * The "booting" method of the trait.
     *
     * @return void
     */
    public static function bootHasCreatedUpdatedBy(): void
    {
        static::creating(function (Model $model) {
            $model->created_by = auth()->user()?->{auth()->user()?->getKeyName()};
            $model->updated_by = auth()->user()?->{auth()->user()?->getKeyName()};
        });

        static::updating(function (Model $model) {
            $model->updated_by = auth()->user()?->{auth()->user()?->getKeyName()};
        });
    }

    /**
     * Initialize the has created updated by trait for an instance.
     *
     * @return void
     */
    public function initializeHasCreatedUpdatedBy(): void
    {
        if (! isset($this->casts['created_by'])) {
            $this->casts['created_by'] = 'integer';
        }

        if (! isset($this->casts['updated_by'])) {
            $this->casts['updated_by'] = 'integer';
        }

        if (! in_array('created_by', $this->guarded)) {
            $this->guarded[] = 'created_by';
        }

        if (! in_array('updated_by', $this->guarded)) {
            $this->guarded[] = 'updated_by';
        }
    }

    /**
     * Get the user that created the model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by', auth()->user()?->getKeyName());
    }

    /**
     * Get the user that updated the model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by', auth()->user()?->getKeyName());
    }
}

