<?php

namespace Pkt\StarterKit;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
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
        Router::class => \Pkt\StarterKit\Macros\RouterMacro::class,
        Route::class => \Pkt\StarterKit\Macros\RouterMacro::class,
    ];


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        foreach ($this->macros as $class => $macro) {
            $class::mixin(new $macro);
        }
    }
}
