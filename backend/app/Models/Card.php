<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'to_do_list_id', 'description',];

    public function list()
    {
        return $this->belongsTo(TodoList::class, 'to_do_list_id');
    }
}
