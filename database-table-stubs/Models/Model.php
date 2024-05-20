<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelName extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $table = 'table_names';
    protected $primaryKey = 'table_name_id';
    protected $guarded = ['table_name_id', 'table_name_uuid'];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['table_name_uuid'];
    }
}
