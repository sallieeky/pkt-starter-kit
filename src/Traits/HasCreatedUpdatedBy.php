<?php

namespace Pkt\StarterKit\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Get the user that created the model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by', auth()->user()?->getKeyName())->withTrashed();
    }

    /**
     * Get the user that updated the model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by', auth()->user()?->getKeyName())->withTrashed();
    }
}

