<?php

namespace Pkt\StarterKit;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Pkt\StarterKit\Helpers\Crypt;

class StarterKitMacroServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Add macros to Blueprint to support created_by and updated_by columns
         * 
         */
        Blueprint::macro('createdUpdatedBy', function () {
            $this->foreignIdFor(config('auth.providers.users.model'), 'created_by')->nullable();
            $this->foreignIdFor(config('auth.providers.users.model'), 'updated_by')->nullable();
        });

        /*
         * Add macros to Blueprint to drop created_by and updated_by columns
         * 
         */
        Blueprint::macro('dropCreatedUpdatedBy', function () {
            $this->dropColumn(['created_by', 'updated_by']);
        });

        /*
         * Add macros to Blueprint to support encrypted columns
         *
         */
        Blueprint::macro('encrypted', function ($column) {
            return $this->addColumn('text', $column);
        });

        /**
         * Add a basic where encrypted clause to the query to support encrypted columns.
         *
         * @param  string $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * @param  string  $boolean = 'and'
         * @return \Illuminate\Database\Query\Builder
         */
        Builder::macro('whereEncrypted', function (string $column, $operator = null, $value = null, string $boolean = 'and'): Builder {
            if (!in_array($operator, ['!=', '=']) || func_num_args() === 2) {
                $value = $operator;
                $operator = '=';
            }
            $value = Crypt::encrypt($value);
            return $this->where($column, $operator, $value, $boolean);
        });

        /**
         * Add a basic or where clause to the query to support encrypted columns.
         *
         * @param  string  $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * @return \Illuminate\Database\Query\Builder
         */
        Builder::macro('orWhereEncrypted', function (string $column, $operator = null, $value = null): Builder {
            return $this->whereEncrypted($column, $operator, $value, 'or');
        });
    }
}
