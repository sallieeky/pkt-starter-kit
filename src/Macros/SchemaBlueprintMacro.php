<?php

namespace Pkt\StarterKit\Macros;

use Illuminate\Database\Schema\Blueprint;

/**
 * Class SchemaBlueprintMacro for adding custom macros to the Blueprint class.
 * 
 * @package Pkt\StarterKit\Macros
 * @method void createdUpdatedBy()
 * @method void dropCreatedUpdatedBy()
 * @method Blueprint encrypted(string $column)
 * 
 * @mixin Blueprint
 */
class SchemaBlueprintMacro
{
    /**
     * Add created_by and updated_by columns to the table.
     *
     * @method void createdUpdatedBy()
     */
    public function createdUpdatedBy(): callable
    {
        return function () {
            $this->foreignIdFor(config('auth.providers.users.model'), 'created_by')->nullable();
            $this->foreignIdFor(config('auth.providers.users.model'), 'updated_by')->nullable();
        };
    }

    /**
     * Drop created_by and updated_by columns from the table.
     *
     * @method void dropCreatedUpdatedBy()
     */
    public function dropCreatedUpdatedBy(): callable
    {
        return function () {
            $this->dropColumn(['created_by', 'updated_by']);
        };
    }

    /**
     * Add encrypted column to support encrypted data to the table.
     *
     * @method Blueprint encrypted(string $column)
     */
    public function encrypted(): callable
    {
        return function ($column) {
            return $this->addColumn('text', $column);
        };
    }
}