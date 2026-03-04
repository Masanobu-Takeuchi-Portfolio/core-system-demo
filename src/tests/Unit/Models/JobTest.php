<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * fillable属性が正しく設定されていることを確認
     */
    public function test_fillable_attributes()
    {
        $job = new Job();
        $expected = [
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
            'osaka_other_subsidy',
        ];

        $this->assertEquals($expected, $job->getFillable());
    }

    /**
     * Jobモデルがファクトリで正常に作成できることを確認
     */
    public function test_job_can_be_created_with_factory()
    {
        $job = Job::factory()->create();

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'user_id' => $job->user_id,
            'year' => $job->year,
            'month' => $job->month,
        ]);
    }

    /**
     * JobモデルがUserとのbelongsToリレーションを持つことを確認
     */
    public function test_job_belongs_to_user()
    {
        $user = User::factory()->create();
        $job = Job::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $job->user);
        $this->assertEquals($user->id, $job->user->id);
    }

    /**
     * Jobモデルのデータ属性にJSON形式の勤務データが格納できることを確認
     */
    public function test_job_data_stores_json()
    {
        $jobData = [
            [
                'date' => '2024-06-11',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'rest' => 1.0,
                'place' => 'Office',
                'worktime' => 8.0,
                'difference' => 0.0,
            ]
        ];

        $job = Job::factory()->create([
            'data' => json_encode($jobData),
        ]);

        $decoded = json_decode($job->data, true);
        $this->assertIsArray($decoded);
        $this->assertEquals('2024-06-11', $decoded[0]['date']);
        $this->assertEquals('09:00', $decoded[0]['start_time']);
        $this->assertEquals('18:00', $decoded[0]['end_time']);
    }

    /**
     * Jobモデルのdiff_totalが正しく保存されることを確認
     */
    public function test_job_diff_total_can_be_stored()
    {
        $job = Job::factory()->create(['diff_total' => '2.5']);

        $this->assertEquals('2.5', $job->diff_total);
    }

    /**
     * Jobモデルの給与関連フィールドが正しく保存できることを確認
     */
    public function test_job_salary_fields_can_be_stored()
    {
        $job = Job::factory()->create([
            'working_days' => '20',
            'overtime_work' => '10',
            'base_salary' => '250000',
            'overtime_charge' => '30000',
            'health_insurance' => '15000',
            'welfare_pension' => '25000',
            'unemployment_insurance' => '2000',
            'income_tax' => '8000',
            'municipal_tax' => '12000',
            'notes' => 'テスト備考',
        ]);

        $this->assertEquals('20', $job->working_days);
        $this->assertEquals('10', $job->overtime_work);
        $this->assertEquals('250000', $job->base_salary);
        $this->assertEquals('30000', $job->overtime_charge);
        $this->assertEquals('15000', $job->health_insurance);
        $this->assertEquals('25000', $job->welfare_pension);
        $this->assertEquals('2000', $job->unemployment_insurance);
        $this->assertEquals('8000', $job->income_tax);
        $this->assertEquals('12000', $job->municipal_tax);
        $this->assertEquals('テスト備考', $job->notes);
    }

    /**
     * Jobモデルの大阪専用フィールドが正しく保存できることを確認
     */
    public function test_job_osaka_fields_can_be_stored()
    {
        $job = Job::factory()->create([
            'osaka_deemed_overtime_pay' => '20000',
            'osaka_traffic_expenses' => '10000',
            'osaka_special_allowance' => '5000',
            'osaka_other_allowances' => '3000',
        ]);

        $this->assertEquals('20000', $job->osaka_deemed_overtime_pay);
        $this->assertEquals('10000', $job->osaka_traffic_expenses);
        $this->assertEquals('5000', $job->osaka_special_allowance);
        $this->assertEquals('3000', $job->osaka_other_allowances);
    }

    /**
     * Jobモデルのその他JSON項目が正しく保存できることを確認
     */
    public function test_job_other_json_fields_can_be_stored()
    {
        $tokyoSupply = json_encode([['tokyo_other_name' => '特別手当', 'tokyo_other' => '5000']]);
        $tokyoSubsidy = json_encode([['tokyo_subsidy_name' => '控除1', 'tokyo_subsidy' => '1000']]);

        $job = Job::factory()->create([
            'tokyo_other_supply' => $tokyoSupply,
            'tokyo_other_subsidy' => $tokyoSubsidy,
        ]);

        $decodedSupply = json_decode($job->tokyo_other_supply, true);
        $decodedSubsidy = json_decode($job->tokyo_other_subsidy, true);

        $this->assertIsArray($decodedSupply);
        $this->assertEquals('特別手当', $decodedSupply[0]['tokyo_other_name']);
        $this->assertIsArray($decodedSubsidy);
    }

    /**
     * ユーザーが削除された場合、関連するJobも削除されることを確認（cascade）
     */
    public function test_job_is_deleted_when_user_is_deleted()
    {
        $user = User::factory()->create();
        $job = Job::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('jobs', ['id' => $job->id]);

        $user->delete();

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }
}
