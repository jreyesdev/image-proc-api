<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Album extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function name(): Attribute
    {
        return new Attribute(
            fn ($value) => Str::title($value),
            fn ($value) => Str::title($value),
        );
    }
}
