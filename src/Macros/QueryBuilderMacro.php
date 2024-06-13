<?php

namespace Pkt\StarterKit\Macros;

use Illuminate\Database\Query\Builder;

/**
 * Class QueryBuilderMacro for adding custom macros to the Query Builder class.
 * 
 * @package Pkt\StarterKit\Macros
 * @method \Illuminate\Database\Query\Builder whereEncrypted(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method \Illuminate\Database\Query\Builder orWhereEncrypted(string $column, $operator = null, $value = null)
 * @method \Illuminate\Database\Query\Builder whereEncryptedIn(string $column, array $values)
 * @method \Illuminate\Database\Query\Builder orWhereEncryptedIn(string $column, array $values)
 * @method \Illuminate\Database\Query\Builder whereEncryptedNotIn(string $column, array $values)
 * @method \Illuminate\Database\Query\Builder orWhereEncryptedNotIn(string $column, array $values)
 * @method \Illuminate\Database\Query\Builder whereEncryptedRelation($relation, $column, $operator = null, $value = null)
 * 
 * @mixin \Illuminate\Database\Query\Builder
 */
class QueryBuilderMacro
{
    // /**
    //  * where encrypted clause query to support encrypted columns
    //  *
    //  * @param  string $column
    //  * @param  mixed  $operator = null
    //  * @param  mixed  $value = null
    //  * @param  string  $boolean = 'and'
    //  * 
    //  * @return \Illuminate\Database\Query\Builder
    //  */
    // Builder::macro('whereEncrypted', function (string $column, $operator = null, $value = null, string $boolean = 'and'): Builder {
    //     if (!in_array($operator, ['!=', '=']) || func_num_args() === 2) {
    //         $value = $operator;
    //         $operator = '=';
    //     }
    //     $value = Crypt::encrypt($value);
    //     return $this->where($column, $operator, $value, $boolean);
    // });

    // /**
    //  * or where encrypted clause query to support encrypted columns
    //  *
    //  * @param  string  $column
    //  * @param  mixed  $operator = null
    //  * @param  mixed  $value = null
    //  * 
    //  * @return \Illuminate\Database\Query\Builder
    //  */
    // Builder::macro('orWhereEncrypted', function (string $column, $operator = null, $value = null): Builder {
    //     return $this->whereEncrypted($column, $operator, $value, 'or');
    // });

    // /**
    //  * where encrypted in clause query to support encrypted columns
    //  *
    //  * @param  string  $column
    //  * @param  array  $values
    //  * 
    //  * @return \Illuminate\Database\Query\Builder
    //  */
    // Builder::macro('whereEncryptedIn', function (string $column, array $values): Builder {
    //     $values = array_map(function ($value) {
    //         return Crypt::encrypt($value);
    //     }, $values);
    //     return $this->whereIn($column, $values);
    // });

    // /**
    //  * or where encrypted in clause query to support encrypted columns
    //  *
    //  * @param  string  $column
    //  * @param  array  $values
    //  * 
    //  * @return \Illuminate\Database\Query\Builder
    //  */
    // Builder::macro('orWhereEncryptedIn', function (string $column, array $values): Builder {
    //     $values = array_map(function ($value) {
    //         return Crypt::encrypt($value);
    //     }, $values);

    //     return $this->orWhereIn($column, $values);
    // });

    // /**
    //  * where encrypted not in clause query to support encrypted columns
    //  *
    //  * @param  string  $column
    //  * @param  array  $values
    //  * 
    //  * @return \Illuminate\Database\Query\Builder
    //  */
    // Builder::macro('whereEncryptedNotIn', function (string $column, array $values): Builder {
    //     $values = array_map(function ($value) {
    //         return Crypt::encrypt($value);
    //     }, $values);
    //     return $this->whereNotIn($column, $values);
    // });

    // /**
    //  * or where encrypted not in clause query to support encrypted columns
    //  *
    //  * @param  string  $column
    //  * @param  array  $values
    //  * 
    //  * @return \Illuminate\Database\Query\Builder
    //  */
    // Builder::macro('orWhereEncryptedNotIn', function (string $column, array $values): Builder {
    //     $values = array_map(function ($value) {
    //         return Crypt::encrypt($value);
    //     }, $values);

    //     return $this->orWhereNotIn($column, $values);
    // });

    // /**
    //  * where encrypted relation clause query to support encrypted columns
    //  *
    //  * @param  string  $relation
    //  * @param  string  $column
    //  * @param  mixed  $operator = null
    //  * @param  mixed  $value = null
    //  * 
    //  * @return \Illuminate\Database\Query\Builder
    //  */
    // Builder::macro('whereEncryptedRelation', function ($relation, $column, $operator = null, $value = null): Builder {
    //     $value = Crypt::encrypt($value);
    //     return $this->whereRelation($relation, $column, $operator, $value);
    // });
}