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

        /**
         * Add a basic where in clause to the query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * @param  string  $boolean = 'and'
         * @param  bool  $not = false
         * @return \Illuminate\Database\Query\Builder
         */
        Builder::macro('whereEncryptedIn', function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereIn($column, $values);
        });

        /**
         * Add a basic or where in clause to the query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * @return \Illuminate\Database\Query\Builder
         */
        Builder::macro('orWhereEncryptedIn', function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);

            return $this->orWhereIn($column, $values);
        });

        /**
         * Add a basic where not in clause to the query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * @param  string  $boolean = 'and'
         * @return \Illuminate\Database\Query\Builder
         */
        Builder::macro('whereEncryptedNotIn', function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereNotIn($column, $values);
        });

        /**
         * Add a basic or where not in clause to the query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * @return \Illuminate\Database\Query\Builder
         */
        Builder::macro('orWhereEncryptedNotIn', function (string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);

            return $this->orWhereNotIn($column, $values);
        });

        /**
         * Add a basic where null clause to the query to support encrypted columns.
         *
         * @param  string  $column
         * @param  string  $boolean = 'and'
         * @param  bool  $not = false
         * @return \Illuminate\Database\Query\Builder
         */
        Builder::macro('whereEncryptedRelation', function ($relation, $column, $operator = null, $value = null): Builder {
            $value = Crypt::encrypt($value);
            return $this->whereRelation($relation, $column, $operator, $value);
        });

        /**
         * Adds a macro to the Builder class that allows eager loading of media collections.
         *
         * @param mixed ...$collectionName The names of the media collections to be loaded.
         * @return \Illuminate\Database\Eloquent\Builder The modified Builder instance.
         */
        Builder::macro('withMedia', function (...$collectionName): Builder {
            return $this->with([
                'media' => fn ($query) => $query->wherePivotIn('collection_name', $collectionName),
            ]);
        });
    }
}
