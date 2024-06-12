<?php

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Pkt\StarterKit\Helpers\Crypt;

/**
 * Helper class to add macros to Blueprint and Builder
 * 
 * @package Pkt\StarterKit
 * @method void createdUpdatedBy()
 * @method void dropCreatedUpdatedBy()
 * @method void encrypted()
 * @method static Builder whereEncrypted(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static Builder orWhereEncrypted(string $column, mixed $operator = null, mixed $value = null)
 * @method static Builder whereInEncrypted(string $column, array $values, string $boolean = 'and', bool $not = false)
 */
class Helpers
{
    /**
     * Add macros to Blueprint and Builder
     *
     * @return void
     */
    public function createdUpdatedBy()
    {
        Blueprint::macro('createdUpdatedBy', function () {
            $this->foreignIdFor(config('auth.providers.users.model'), 'created_by')->nullable();
            $this->foreignIdFor(config('auth.providers.users.model'), 'updated_by')->nullable();
        });
    }

    /**
     * Add macros to Blueprint to drop created_by and updated_by columns
     *
     * @return void
     */
    public function dropCreatedUpdatedBy()
    {
        Blueprint::macro('dropCreatedUpdatedBy', function () {
            $this->dropColumn(['created_by', 'updated_by']);
        });
    }

    /**
     * Add macros to Blueprint to support encrypted columns
     *
     * @return void
     */
    public function encrypted()
    {
        Blueprint::macro('encrypted', function ($column) {
            return $this->addColumn('text', $column);
        });
    }

    /**
     * Add a basic where encrypted clause to the query to support encrypted columns.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * 
     * @return Builder
     */
    public function whereEncrypted($column, $operator = null, $value = null, $boolean = 'and')
    {
        Builder::macro('whereEncrypted', function ($column, $operator = null, $value = null, $boolean = 'and') {
            $crypt = new Crypt();
            $column = $crypt->encrypt($column);
            $value = $crypt->encrypt($value);
            return $this->where($column, $operator, $value, $boolean);
        });
    }

    /**
     * Add a basic or where clause to the query to support encrypted columns.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return Builder
     */
    public function orWhereEncrypted($column, $operator = null, $value = null)
    {
        Builder::macro('orWhereEncrypted', function ($column, $operator = null, $value = null) {
            return $this->whereEncrypted($column, $operator, $value, 'or');
        });
    }

    /**
     * Add a basic where in clause to the query to support encrypted columns.
     *
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @return Builder
     */
    public function whereInEncrypted($column, $values, $boolean = 'and', $not = false)
    {
        Builder::macro('whereInEncrypted', function ($column, $values, $boolean = 'and', $not = false) {
            $crypt = new Crypt();
            $column = $crypt->encrypt($column);
            $values = $crypt->encrypt($values);
            return $this->whereIn($column, $values, $boolean, $not);
        });
    }
}