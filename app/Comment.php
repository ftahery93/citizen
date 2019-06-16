<?php

namespace App;

use App\Poll;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //otherwise the user_id won't save properly.
    public $incrementing = false;

    protected $fillable = [
        'date',
        'comment',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::uuid();
        });
    }

    public function polls() {
        return $this->belongsToMany(Poll::class);
    }
}
