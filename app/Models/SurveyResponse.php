<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use App\Services\Helpers;
use App\Models\Survey;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'survey_id',
        'collector_id',
        'longitude',
        'latitude',
        'data'

    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }


    public function getDataAttribute($data)
    {
        $datas = json_decode($data, true);
        $rawSurveyData = Survey::where('id', 3)->firstOrFail();
        $rawSurveyDataDecoded = json_decode($rawSurveyData->data, true);
        $datas = Helpers::resolveSurveyLabelInconsistencies($rawSurveyDataDecoded, $datas);
        $datas = Helpers::removeWidgetFromResponseIfNotInSurvey($rawSurveyDataDecoded, $datas);
        $datas = Helpers::addWidgetToResponseIfInSurvey($rawSurveyDataDecoded, $datas);
        return  json_encode($datas);
    }
}
