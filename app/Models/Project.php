<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_name',
        'location',
        'status' ,
        'no_of_rooms',
        'no_of_floors',
        'start_date',
        'assign_employees'
    ];
    
    public function users()
    {
        return $this->belongsToMany(User::class,'project_assigned_details','project_id','user_id');
    }
}
