<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'employee_expenses';

    protected $fillable = [
        'employee_id',
        'vehicle_id',
        'project_id',
        'expense',
        'expence_details',
        'status'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class,'vehicle_id','id');
    }
}
