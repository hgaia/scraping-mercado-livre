<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class scrape extends Model
{
    use HasFactory;
    protected $table = "scrapes";

    protected $fillable = [
        'product_search',
        'image_url',
        'price',
        'name',
    ];
}
