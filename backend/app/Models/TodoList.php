<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDoList extends Model
{
    use HasFactory;

    protected $table = 'to_do_lists';
    protected $fillable = ['title', 'board_id'];

    public function cards()
    {
        return $this->hasMany(Card::class, 'to_do_list_id');
    }
}
