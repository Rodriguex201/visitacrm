<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'xxxxcity';

    protected $primaryKey = 'citycodigo';

    public $timestamps = false;

    protected $guarded = [];
}

