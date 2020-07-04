<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = "employee";
    protected $fillable = ['email', 'password'];
    protected $hidden = ['created_at', 'updated_at', 'password'];
}
