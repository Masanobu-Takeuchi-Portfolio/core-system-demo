<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [ // DBにカラムを複数代入するためにはここにカラム名を追加する必要あり。
        'user_id',
        'year',
        'month',
        'data',
        'diff_total',
        'working_days',
        'overtime_work',
        'base_salary',
        'overtime_charge',
        'traffic_expenses',
        'nomination_fee',
        'all_payment',
        'health_insurance',
        'welfare_pension',
        'unemployment_insurance',
        'income_tax',
        'municipal_tax',
        'total_deductions',
        'osaka_deemed_overtime_pay',
        'osaka_traffic_expenses',
        'osaka_special_allowance',
        'osaka_other_allowances',
        'notes',
        'tokyo_other_supply',
        'tokyo_other_subsidy',
        'osaka_other_supply',
        'osaka_other_subsidy'
    ];

    public function user()
    {
        // 子->親のデータを関連づける場合。親は１つなので単数名称(user)
        return $this->belongsTo(User::class);
    }
}
