<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = "countries";
    protected $fillable = array('id', 'name_en', 'name_ar', 'country_code', 'status');
    protected $hidden = array('created_at', 'updated_at');
}
