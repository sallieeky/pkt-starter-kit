<?php

namespace Pkt\StarterKit\Helpers;
use Illuminate\Support\Str;

class GlobalSearch
{
    public static $models = [];
    public static $searchableAttributes = [];

    public static function registerModel($model)
    {
        static::$models[] = $model;
    }

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

    public static function search($query)
    {
        $results = collect(static::$models)->map(function ($model) use ($query) {
            return $model->searchableEloquentQuery()->where(function ($queryBuilder) use ($model, $query) {
                foreach ($model->searchableAttributes() as $attribute) {
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

    public static function isEnable()
    {
        return !empty(static::$models);
    }
}
