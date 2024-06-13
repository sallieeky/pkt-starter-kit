<?php

namespace Pkt\StarterKit;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class StarterKitMacroServiceProvider extends ServiceProvider
{
    /**
     * The macro mixin mappings.
     *
     * @var array
     */
    protected $macros = [
        Blueprint::class => \Pkt\StarterKit\Macros\SchemaBlueprintMacro::class,
        Builder::class => \Pkt\StarterKit\Macros\QueryBuilderMacro::class,
        EloquentBuilder::class => \Pkt\StarterKit\Macros\EloquentBuilderMacro::class,
    ];


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->macros as $class => $macro) {
            $class::mixin(new $macro);
        }
    }
}
