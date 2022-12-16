<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopPos extends Model
{
    public $timestamps = false;

    protected $fillable = ['category', 'parentCategory', 'position', 'date'];

    use HasFactory;
}
