<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'manufacturer' ,
        'model',
        'serial_no',
        'barcode',
        'category',
        'avail',
        'loaned_to',
        'purchase_date',
        'price',
        'warranty_date',
        'web_url',
        'manual_url',
        'status'
    ];
}
