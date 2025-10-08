<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    // 親->子 複数形
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
