<?php

namespace ESIK\Models\ESI;

use Illuminate\Database\Eloquent\Model;

use ESIK\Models\MemberLocation;

class Station extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'stations';
    public $incrementing = false;
    protected static $unguarded = true;

    public function location_info ()
    {
        return $this->morphOne(MemberLocation::class);
    }

    public function clone_info ()
    {
        return $this->morphOne(MemberLocation::class);
    }
}