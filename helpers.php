<?php

namespace Illuminate\Database\Query {

    use Pkt\StarterKit\Helpers\Crypt;

    class Builder extends \Illuminate\Database\Query\Builder
    {
        /**
         * Where encrypted clause query to support encrypted columns.
         *
         * @param  string $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * @param  string  $boolean = 'and'
         * 
         * @return \Illuminate\Database\Query\Builder
         */
        public function whereEncrypted(string $column, $operator = null, $value = null, string $boolean = 'and'): Builder {
            if (!in_array($operator, ['!=', '=']) || func_num_args() === 2) {
                $value = $operator;
                $operator = '=';
            }
            $value = Crypt::encrypt($value);
            return $this->where($column, $operator, $value, $boolean);
        }

        /**
         * Or where encrypted clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * 
         * @return \Illuminate\Database\Query\Builder
         */
        public function orWhereEncrypted(string $column, $operator = null, $value = null): Builder {
            return $this->whereEncrypted($column, $operator, $value, 'or');
        }

        /**
         * Where encrypted in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * @param  string  $boolean = 'and'
         * @param  bool  $not = false
         * 
         * @return \Illuminate\Database\Query\Builder
         */
        public function whereEncryptedIn(string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereIn($column, $values);
        }

        /**
         * Or where encrypted in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * 
         * @return \Illuminate\Database\Query\Builder
         */
        public function orWhereEncryptedIn(string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->orWhereIn($column, $values);
        }

        /**
         * Where encrypted not in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * 
         * @return \Illuminate\Database\Query\Builder
         */
        public function whereEncryptedNotIn(string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereNotIn($column, $values);
        }

        /**
         * Or where encrypted not in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * 
         * @return \Illuminate\Database\Query\Builder
         */
        public function orWhereEncryptedNotIn(string $column, array $values): Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->orWhereNotIn($column, $values);
        }

        /**
         * Where encrypted relation clause query to support encrypted columns.
         *
         * @param  string  $relation
         * @param  string  $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * 
         * @return \Illuminate\Database\Query\Builder
         */
        public function whereEncryptedRelation($relation, $column, $operator = null, $value = null): Builder {
            $value = Crypt::encrypt($value);
            return $this->whereRelation($relation, $column, $operator, $value);
        }
    }
}

namespace Illuminate\Database\Eloquent {

    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Pkt\StarterKit\Helpers\Crypt;

    class Builder extends \Illuminate\Database\Eloquent\Builder
    {
        /**
         * Where encrypted clause query to support encrypted columns.
         *
         * @param  string $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * @param  string  $boolean = 'and'
         * 
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function whereEncrypted(string $column, $operator = null, $value = null, string $boolean = 'and'): \Illuminate\Database\Eloquent\Builder {
            if (!in_array($operator, ['!=', '=']) || func_num_args() === 2) {
                $value = $operator;
                $operator = '=';
            }
            $value = Crypt::encrypt($value);
            return $this->where($column, $operator, $value, $boolean);
        }

        /**
         * Or where encrypted clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * 
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function orWhereEncrypted(string $column, $operator = null, $value = null): \Illuminate\Database\Eloquent\Builder {
            return $this->whereEncrypted($column, $operator, $value, 'or');
        }

        /**
         * Where encrypted in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * @param  string  $boolean = 'and'
         * @param  bool  $not = false
         * 
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function whereEncryptedIn(string $column, array $values): \Illuminate\Database\Eloquent\Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereIn($column, $values);
        }

        /**
         * Or where encrypted in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * 
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function orWhereEncryptedIn(string $column, array $values): \Illuminate\Database\Eloquent\Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->orWhereIn($column, $values);
        }

        /**
         * Where encrypted not in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * 
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function whereEncryptedNotIn(string $column, array $values): \Illuminate\Database\Eloquent\Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->whereNotIn($column, $values);
        }

        /**
         * Or where encrypted not in clause query to support encrypted columns.
         *
         * @param  string  $column
         * @param  array  $values
         * 
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function orWhereEncryptedNotIn(string $column, array $values): \Illuminate\Database\Eloquent\Builder {
            $values = array_map(function ($value) {
                return Crypt::encrypt($value);
            }, $values);
            return $this->orWhereNotIn($column, $values);
        }

        /**
         * Where encrypted relation clause query to support encrypted columns.
         *
         * @param  string  $relation
         * @param  string  $column
         * @param  mixed  $operator = null
         * @param  mixed  $value = null
         * 
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function whereEncryptedRelation($relation, $column, $operator = null, $value = null): \Illuminate\Database\Eloquent\Builder {
            $value = Crypt::encrypt($value);
            return $this->whereRelation($relation, $column, $operator, $value);
        }

        /**
         * Eeager loading of media collections to the query.
         *
         * @param mixed ...$collectionName The names of the media collections to be loaded.
         * @return \Illuminate\Database\Eloquent\Builder The modified Builder instance.
         */
        public function withMedia(...$collectionName): \Illuminate\Database\Eloquent\Builder {
            return $this->with([
                'media' => function (MorphToMany $query) use ($collectionName) {
                    $query->wherePivotIn('collection_name', $collectionName);
                }
            ]);
        }
    }
}

namespace Illuminate\Database\Schema {

    class Blueprint extends \Illuminate\Database\Schema\Blueprint
    {
        /**
         * Add a created_by and updated_by columns to the table.
         *
         * @return void
         */
        public function createdUpdatedBy(): void {
            $this->foreignIdFor(config('auth.providers.users.model'), 'created_by')->nullable();
            $this->foreignIdFor(config('auth.providers.users.model'), 'updated_by')->nullable();
        }

        /**
         * Drop the created_by and updated_by columns from the table.
         *
         * @return void
         */
        public function dropCreatedUpdatedBy(): void {
            $this->dropColumn(['created_by', 'updated_by']);
        }

        /**
         * Add an encrypted column to the table.
         *
         * @param  string  $column
         * @return \Illuminate\Database\Schema\ColumnDefinition
         */
        public function encrypted(string $column): ColumnDefinition {
            return $this->addColumn('text', $column);
        }
    }

}
