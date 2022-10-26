<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = ['name', 'type','plus_words_client','minus_words_client','plus_words_operator','minus_words_operator'];
}
