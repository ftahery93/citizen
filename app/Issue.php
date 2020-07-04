<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = "issue";
    protected $fillable = [
        'image', 'description', 'fullname', 'mobile', 'latitude', 'longitude', 'user_id', 'status', 'approved_date', 'approved_image', 'employee_id',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function getImageAttribute($value)
    {
        return $value ? url('/public/uploads/issue_images/' . $value) : null;
    }

    public function getApprovedImageAttribute($value)
    {
        return $value ? url('/public/uploads/approved_issue_images/' . $value) : null;
    }
}
