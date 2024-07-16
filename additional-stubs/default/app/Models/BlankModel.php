<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Pkt\StarterKit\Traits\HasCreatedUpdatedBy;

class ModelName extends Model
{
    // use SoftDeletes, HasCreatedUpdatedBy;

    protected $table = 'table_names';
    protected $primaryKey = 'table_name_id';
    protected $guarded = ['table_name_id'];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ...
    ];
}
