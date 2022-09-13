<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class MailingList extends Model
{
    use HasFactory;

    protected $with = ['contacts'];

    protected $fillable = [
        'uuid',
        'name',
        'user_id'

    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }


    public function contacts()
    {
        return  $this->hasMany(MailingListContact::class);
    }
}
