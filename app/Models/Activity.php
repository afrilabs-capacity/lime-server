<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\Survey;

class Activity extends Model
{
    use HasFactory;

    protected $appends = ['model_data'];

    protected $fillable = [
        'uuid',
        'model_uuid',
        'event',
        'model_id',
        'user_id',
        'model'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }


    public function getModelDataAttribute()
    {

        if ($this->model == "User") {
            return User::find($this->model_id);
        }

        if ($this->model == "Survey") {
            return Survey::find($this->model_id);
        }
    }
}
