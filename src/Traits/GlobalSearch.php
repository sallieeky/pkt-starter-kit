<?php

namespace Pkt\StarterKit\Traits;

use Illuminate\Database\Eloquent\Model;
use Pkt\StarterKit\Helpers\GlobalSearch as HelpersGlobalSearch;

trait GlobalSearch
{
    /**
     * The "boot" method of the model.
     *
     * @return void
     */
    public static function bootGlobalSearch(): void
    {
        HelpersGlobalSearch::registerModel(new static());
    }

    /**
     * Get the eloquent query.
     *
     * @return object
     */
    public function searchableEloquentQuery(): object
    {
        return $this->query();
    }

    /**
     * Get the columns that can be searched.
     *
     * @return array
     */
    public function searchableAttributes(): array
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    /**
     * Get the columns id that can be searched.
     *
     * @return int|string
     */
    public function searchableAttributeId(): int|string
    {
        return $this->getKey();
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return string
     */
    public function searchableFormatRecord(Model $record): string
    {
        return (string) $record[$this->getKeyName()];
    }

    /**
     * Get action url for searchable record.
     *
     * @return ?string
     */
    public function searchableRecordActionUrl(Model $record): ?string
    {
        return null;
    }
}
