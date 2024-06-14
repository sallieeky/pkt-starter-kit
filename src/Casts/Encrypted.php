<?php

namespace Pkt\StarterKit\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Pkt\StarterKit\Helpers\Crypt;

class Encrypted implements CastsAttributes
{
    /**
     * Create a new encrypted cast.
     *
     * @param  string|null  $actualType
     */
    public function __construct(
        protected string|null $actualType = 'string'
    ){}

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Crypt::decrypt($value)
            ? match ($this->actualType) {
                'int' => (int) Crypt::decrypt($value),
                'integer' => (int) Crypt::decrypt($value),
                'float' => (float) Crypt::decrypt($value),
                'bool' => (bool) Crypt::decrypt($value),
                'boolean' => (bool) Crypt::decrypt($value),
                'array' => json_decode(Crypt::decrypt($value), true),
                'object' => json_decode(Crypt::decrypt($value)),
                'collection' => collect(json_decode(Crypt::decrypt($value), true)),
                default => Crypt::decrypt($value),
            }
            : null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Crypt::encrypt($value);
    }
}
