<?php

namespace ESIK\Models\SDE;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'regions';
    public $incrementing = false;
    protected static $unguarded = true;
}