<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'data',
        'notes'
    ];

    public function user()
    {
        // 子->親のデータを関連づける場合。親は１つなので単数名称(user)
        return $this->belongsTo(User::class);
    }
}
