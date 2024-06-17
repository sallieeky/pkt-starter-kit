<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Pkt\StarterKit\Traits\GlobalSearch;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUuids, SoftDeletes, GlobalSearch;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'npk',
        'email',
        'password',
        'is_active',
        'hierarchy_code',
        'position_id',
        'position',
        'work_unit_id',
        'work_unit',
        'user_flag',
        'user_alias',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['user_uuid'];
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        parent::booted();

        static::created(function (User $user) {
            $user->notify(new \Pkt\StarterKit\Notifications\Notification(
                'Welcome! 🎉',
                'Welcome to ' . config('app.name') . '.',
                route('home')
            ));
        });
    }

    /**
     * Get the columns that can be searched.
     *
     * @return array
     */
    public function searchableAttributes(): array
    {
        return [
            'username',
            'name',
            'npk',
            'email',
        ];
    }

    /**
     * Get the columns id that can be searched.
     *
     * @return int|string
     */
    public function searchableAttributeId(): int|string
    {
        return $this->user_id;
    }

    /**
     * Get action url for searchable record.
     *
     * @param  object  $record
     * @return ?string
     */
    public function searchableRecordActionUrl($record): ?string
    {
        return route('user.browse');
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @param  object  $record
     * @return string
     */
    public function searchableFormatRecord($record): string
    {
        return $record->npk . ' - ' . $record->name;
    }

    /**
     * Get the eloquent query.
     *
     * @return ?object
     */
    public function searchableEloquentQuery(): ?object
    {
        if (auth()->user()?->hasRole('Superadmin')) {
            return $this->query()->where('is_active', true);
        }
        return null;
    }
}