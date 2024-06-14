<?php

namespace Pkt\StarterKit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Pkt\StarterKit\Macros\SchemaBlueprintMacro createdUpdatedBy()
 * @method static \Pkt\StarterKit\Macros\SchemaBlueprintMacro dropCreatedUpdatedBy()
 * @method static \Pkt\StarterKit\Macros\SchemaBlueprintMacro encrypted(string $column)
 * 
 * @see \Pkt\StarterKit\Macros\SchemaBlueprintMacro
 */
class Blueprint extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'blueprint';
    }
}
