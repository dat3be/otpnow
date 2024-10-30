<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    // Khai báo các trường có thể gán giá trị qua mass assignment
    protected $fillable = ['name', 'description', 'price'];
}
