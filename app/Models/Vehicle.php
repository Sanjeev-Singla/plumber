<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'vehicle_no',
        'description',
        'manufacturer' ,
        'model',
        'type',
        'km',
        'status',
        'alloted_user_id'
    ];

    /**
     * Get the user that owns the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function allotedUser()
    {
        return $this->belongsTo(User::class, 'alloted_user_id', 'id');
    }
}
