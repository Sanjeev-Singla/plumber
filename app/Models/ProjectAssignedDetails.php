<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAssignedDetails extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id','project_id','assigned_date' ,'unassigned_date',
    ];
    
   
}
