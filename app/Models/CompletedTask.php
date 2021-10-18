<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'completion_date',
    ];

    protected $casts = [
        'completion_date' => 'date:Y-m-d',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
