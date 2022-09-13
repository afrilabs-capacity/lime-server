<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SurveyResponse;
use Ramsey\Uuid\Uuid;

class Survey extends Model
{
    use HasFactory;

    protected $appends = ['responses', 'project_name', 'project_uuid', 'survey_users'];
    protected $fillable = [
        'uuid',
        'name',
        'data',
        'project_id',
        'start_date',
        'end_date',
        'longitude',
        'latitude',
        'pinned'

    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }

    public function getResponsesAttribute()
    {

        return  surveyResponse::where('survey_id', $this->id)->get()->count();
    }

    public function getProjectNameAttribute()
    {
        if (!is_null($this->project_id)) {
            return Project::where('id', $this->project_id)->first()->name;
        }
        return "Unnamed";
    }

    public function getProjectUuidAttribute()
    {
        if (!is_null($this->project_id)) {
            return Project::where('id', $this->project_id)->first()->uuid;
        }
    }

    public function getSurveyUsersAttribute()
    {
        if (!is_null($this->project_id)) {
            return  $this->users()->count();
        }
    }


    public function users()
    {

        return  $this->belongsToMany(User::class, 'survey_users');
    }


    public function responses()
    {

        return  $this->hasMany(SurveyResponse::class);
    }
}
