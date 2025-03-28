<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function todoLists()
    {
        return $this->hasMany(TodoList::class);
    }
}
