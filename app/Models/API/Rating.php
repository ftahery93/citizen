<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = "ratings";

    protected $fillable = ['user_id', 'company_id', 'rating'];
}
