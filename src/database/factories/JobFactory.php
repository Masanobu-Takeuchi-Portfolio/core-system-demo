<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'year' => 2024,
            'month' => 7,
            'data' => json_encode([
                [
                    'date' => '2024-06-11',
                    'start_time' => '09:00',
                    'start_timestamp' => strtotime('2024-06-11 09:00'),
                    'end_time' => '18:00',
                    'end_timestamp' => strtotime('2024-06-11 18:00'),
                    'rest' => 1.0,
                    'place' => 'Office',
                    'worktime' => 8.0,
                    'difference' => 0.0,
                ]
            ]),
            'diff_total' => '0.0',
            'working_days' => null,
            'overtime_work' => null,
            'base_salary' => null,
            'overtime_charge' => null,
            'traffic_expenses' => null,
            'nomination_fee' => null,
            'all_payment' => null,
            'health_insurance' => null,
            'welfare_pension' => null,
            'unemployment_insurance' => null,
            'income_tax' => null,
            'municipal_tax' => null,
            'total_deductions' => null,
            'osaka_deemed_overtime_pay' => null,
            'osaka_traffic_expenses' => null,
            'osaka_special_allowance' => null,
            'osaka_other_allowances' => null,
            'notes' => null,
            'tokyo_other_supply' => null,
            'tokyo_other_subsidy' => null,
            'osaka_other_supply' => null,
            'osaka_other_subsidy' => null,
        ];
    }
}
