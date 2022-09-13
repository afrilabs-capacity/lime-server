<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Project extends Model
{
    use HasFactory;

    protected $with = ['surveys', 'users'];

    protected $fillable = [
        'uuid',
        'name',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }

    public function surveys()
    {

        return  $this->hasMany(Survey::class);
    }

    public function users()
    {

        return  $this->belongsToMany(User::class, 'project_users');
    }
}
