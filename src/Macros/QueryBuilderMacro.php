<?php

namespace Pkt\StarterKit\Macros;

use Illuminate\Database\Query\Builder;
use Pkt\StarterKit\Casts\Encrypted;
use Pkt\StarterKit\Helpers\Crypt;

class QueryBuilderMacro
{
    /**
     * where encrypted clause query to support encrypted columns
     *
     * @param  string $column
     * @param  mixed  $operator = null
     * @param  mixed  $value = null
     * @param  string  $boolean = 'and'
     * 
     * @return \Illuminate\Database\Query\Builder
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
     * @return \Illuminate\Database\Query\Builder
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
     * @return \Illuminate\Database\Query\Builder
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
     * @return \Illuminate\Database\Query\Builder
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
     * @return \Illuminate\Database\Query\Builder
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
     * @return \Illuminate\Database\Query\Builder
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
     * @return \Illuminate\Database\Query\Builder
     */
    public function whereEncryptedRelation(): callable
    {
        return function ($relation, $column, $operator = null, $value = null): Builder {
            if (!in_array($operator, ['!=', '=']) || func_num_args() === 3) {
                $value = $operator;
                $operator = '=';
            }
            $value = Crypt::encrypt($value);
            return $this->whereRelation($relation, $column, $operator, $value);
        };
    }

    /**
     * or where encrypted relation clause query to support encrypted columns
     *
     * @param  string  $relation
     * @param  string  $column
     * @param  mixed  $operator = null
     * @param  mixed  $value = null
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orWhereEncryptedRelation(): callable
    {
        return function (string $relation, string $column, $operator = null, $value = null): Builder {
            if (!in_array($operator, ['!=', '=']) || func_num_args() === 3) {
                $value = $operator;
                $operator = '=';
            }
            $value = Crypt::encrypt($value);
            return $this->orWhereRelation($relation, $column, $operator, $value);
        };
    }

    /**
     * where search clause query to support search columns
     *
     * @param  array  $columns
     * @param  mixed  $value
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    public function search(): callable
    {
        return function (array $columns, $value): Builder {
            return $this->where(function ($query) use ($columns, $value) {
                foreach ($columns as $column) {
                    if (str_contains($column, '.')) {
                        $relation = explode('.', $column)[0];
                        $column = explode('.', $column)[1];

                        if (optional($this->getModel()->getCasts())[$column] === Encrypted::class) {
                            $query->orWhereEncryptedRelation($relation, $column, $value);
                        } else {
                            $query->orWhereRelation($relation, $column, 'like', "%$value%");
                        }
                    } else {
                        if (optional($this->getModel()->getCasts())[$column] === Encrypted::class) {
                            $query->orWhereEncrypted($column, $value);
                        } else {
                            $query->orWhere($column, 'like', "%$value%");
                        }
                    }
                }
            });
        };
    }

}