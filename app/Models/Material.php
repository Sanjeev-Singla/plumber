<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable= [
        'project_id',
        'request_user_id',
        'description',
        'issue_date',
        'admin_remarks',
        'received_date',
        'user_remarks',
        'status'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'request_user_id','id');
    }
}