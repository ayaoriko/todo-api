<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    public $timestamps = false;
    public $fillable = ['name', 'is_check', 'category_id'];

    public function index()
    {
        $todos = Todo::all();
    }
}
