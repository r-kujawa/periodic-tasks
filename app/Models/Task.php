<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'start_date',
        'end_date',
        'repeat',
        'week_days',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completed()
    {
        return $this->hasMany(CompletedTask::class);
    }

    public function getWeekDaysAttribute()
    {
        return explode(',', $this->attributes['week_days'] ?? '');
    }
}
