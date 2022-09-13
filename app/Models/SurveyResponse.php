<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'survey_id',
        'collector_id',
        'data'

    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }
}
