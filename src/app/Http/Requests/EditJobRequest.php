<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'working_days' => ['integer', 'nullable'],
            'overtime_work' => ['integer', 'nullable'],
            'base_salary' => ['integer', 'nullable'],
            'overtime_charge' => ['integer', 'nullable'],
            'nomination_fee' => ['integer', 'nullable'],
            'health_insurance' => ['integer', 'nullable'],
            'welfare_pension' => ['integer', 'nullable'],
            'unemployment_insurance' => ['integer', 'nullable'],
            'income_tax' => ['integer', 'nullable'],
            'municipal_tax' => ['integer', 'nullable'],
            'osaka_deemed_overtime_pay' => ['integer', 'nullable'],
            'osaka_special_allowance' => ['integer', 'nullable'],
            'osaka_other_allowances' => ['integer', 'nullable'],
            'osaka_traffic_expenses' => ['integer', 'nullable'],
            'tokyo_other.*' => ['integer', 'nullable'],
            'tokyo_other_subsidy.*' => ['integer', 'nullable'],
            'osaka_other.*' => ['integer', 'nullable'],
            'osaka_other_subsidy.*' => ['integer', 'nullable']
        ];
    }
}
