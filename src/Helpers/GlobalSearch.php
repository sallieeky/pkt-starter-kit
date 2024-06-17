<?php

namespace Pkt\StarterKit\Helpers;

use Pkt\StarterKit\Casts\Encrypted;
use Illuminate\Support\Str;

class GlobalSearch
{
    /**
     * The models that should be searchable.
     *
     * @var array
     */
    public static $models = [];

    /**
     * The columns that can be searched.
     *
     * @var array
     */
    public static $searchableAttributes = [];

    /**
     * Register a model to be searchable.
     *
     * @param  object  $model
     * @return void
     */
    public static function registerModel($model)
    {
        static::$models[] = $model;
    }


    /**
     * Construct the helper.
     *
     * @return void
     */
    public function __construct()
    {
        $models = array_diff(scandir(app_path('Models')), ['.', '..']);
        foreach ($models as $model) {
            $model = 'App\Models\\' . str_replace('.php', '', $model);
            if (class_exists($model)) {
                new $model;
            }
        }
    }

    /**
     * Search for a record query.
     *
     * @param  string  $query
     * @return \Illuminate\Support\Collection
     */
    public static function search($query)
    {
        $results = collect(static::$models)->map(function ($model) use ($query) {
            return ($model->searchableEloquentQuery() ?? $model->where($model->getKeyName(), -103918291))->where(function ($queryBuilder) use ($model, $query) {
                foreach ($model->searchableAttributes() as $attribute) {
                    if (isset($model->getCasts()[$attribute]) && str_contains($model->getCasts()[$attribute], Encrypted::class)) {
                        $queryBuilder->orWhereEncrypted($attribute, $query);
                        continue;
                    }
                    $queryBuilder->orWhere($attribute, 'like', '%' . $query . '%');
                }
            })->get();
        })->flatten()->groupBy(function ($item) {
            return Str::headline(class_basename($item));
        })->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'id' => $item->searchableAttributeId(),
                    'value' => $item->searchableFormatRecord($item),
                    'url' => $item->searchableRecordActionUrl($item)
                ];
            });
        });

        return $results;
    }

    /**
     * Check if the global search is enabled by any model.
     *
     * @return bool
     */
    public static function isEnable()
    {
        return !empty(static::$models) && collect(static::$models)->filter(function ($model) {
            return !is_null($model->searchableEloquentQuery());
        })->isNotEmpty();
    }
}
