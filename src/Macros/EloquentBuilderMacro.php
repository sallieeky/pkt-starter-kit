<?php

namespace Pkt\StarterKit\Macros;

use Illuminate\Database\Eloquent\Builder;
use Pkt\StarterKit\Helpers\Crypt;

/**
 * Class EloquentBuilderMacro for adding custom macros to the Eloquent Builder class.
 * 
 * @package Pkt\StarterKit\Macros
 * @method \Illuminate\Database\Eloquent\Builder whereEncrypted(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method \Illuminate\Database\Eloquent\Builder orWhereEncrypted(string $column, $operator = null, $value = null)
 * @method \Illuminate\Database\Eloquent\Builder whereEncryptedIn(string $column, array $values)
 * @method \Illuminate\Database\Eloquent\Builder orWhereEncryptedIn(string $column, array $values)
 * @method \Illuminate\Database\Eloquent\Builder whereEncryptedNotIn(string $column, array $values)
 * @method \Illuminate\Database\Eloquent\Builder orWhereEncryptedNotIn(string $column, array $values)
 * @method \Illuminate\Database\Eloquent\Builder whereEncryptedRelation($relation, $column, $operator = null, $value = null)
 * @method \Illuminate\Database\Eloquent\Builder withMedia(...$collectionName)
 * 
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class EloquentBuilderMacro
{
    /**
     * where encrypted clause query to support encrypted columns
     *
     * @param  string $column
     * @param  mixed  $operator = null
     * @param  mixed  $value = null
     * @param  string  $boolean = 'and'
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function whereEncrypted(): callable
    {
        return function (string $column, $operator = null, $value = null, string $boolean = 'and'): Builder {
            if (!in_array($operator, ['!=', '=']) || func_num_args() === 2) {
                $value = $operator;
                $operator = '=';
            }
            $value = Crypt::encrypt($value);
            return $this->where($column, $operator, $value, $boolean);
        };
    }
    /**
     * or where encrypted clause query to support encrypted columns
     *
     * @param  string  $column
     * @param  mixed  $operator = null
     * @param  mixed  $value = null
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orWhereEncrypted(): callable
    {
        return function (string $column, $operator = null, $value = null): Builder {
            return $this->whereEncrypted($column, $operator, $value, 'or');
        };
    }

    /**
     * where encrypted in clause query to support encrypted columns
     *
     * @param  string  $column
     * @param  array  $values
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function whereEncryptedIn(): callable
    {
        return function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereIn($column, $values);
        };
    }

    /**
     * or where encrypted in clause query to support encrypted columns
     *
     * @param  string  $column
     * @param  array  $values
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orWhereEncryptedIn(): callable
    {
        return function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);

            return $this->orWhereIn($column, $values);
        };
    }

    /**
     * where encrypted not in clause query to support encrypted columns
     *
     * @param  string  $column
     * @param  array  $values
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function whereEncryptedNotIn(): callable
    {
        return function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereNotIn($column, $values);
        };
    }

    /**
     * or where encrypted not in clause query to support encrypted columns
     *
     * @param  string  $column
     * @param  array  $values
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orWhereEncryptedNotIn(): callable
    {
        return function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);

            return $this->orWhereNotIn($column, $values);
        };
    }

    /**
     * where encrypted relation clause query to support encrypted columns
     *
     * @param  string  $relation
     * @param  string  $column
     * @param  mixed  $operator = null
     * @param  mixed  $value = null
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function whereEncryptedRelation(): callable
    {
        return function (string $relation, string $column, $operator = null, $value = null): Builder {
            $value = Crypt::encrypt($value);
            return $this->whereRelation($relation, $column, $operator, $value);
        };
    }

    /**
     * with media clause query that allows eager loading of media collections.
     *
     * @param mixed ...$collectionName The names of the media collections to be loaded.
     * @return \Illuminate\Database\Eloquent\Builder The modified Builder instance.
     */
    public function withMedia(): callable
    {
        return function (...$collectionName): Builder {
            return $this->with([
                'media' => fn ($query) => $query->wherePivotIn('collection_name', $collectionName),
            ]);
        };
    }
}