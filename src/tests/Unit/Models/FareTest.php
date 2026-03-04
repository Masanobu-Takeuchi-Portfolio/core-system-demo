<?php

namespace Tests\Unit\Models;

use App\Models\fare;
use App\Models\User;
use Database\Factories\FareFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * FareFactoryのインスタンスを取得するヘルパー
     * fareモデルがクラス名小文字のため、Laravel標準のfactory()では解決できないため
     */
    private function fareFactory(): FareFactory
    {
        return FareFactory::new();
    }

    /**
     * fillable属性が正しく設定されていることを確認
     */
    public function test_fillable_attributes()
    {
        $fareModel = new fare();
        $expected = [
            'user_id',
            'year',
            'month',
            'data',
            'first_total',
            'second_total',
            'osaka_total',
        ];

        $this->assertEquals($expected, $fareModel->getFillable());
    }

    /**
     * Fareモデルがファクトリで正常に作成できることを確認
     */
    public function test_fare_can_be_created_with_factory()
    {
        $fareRecord = $this->fareFactory()->create();

        $this->assertDatabaseHas('fares', [
            'id' => $fareRecord->id,
            'user_id' => $fareRecord->user_id,
            'year' => $fareRecord->year,
            'month' => $fareRecord->month,
        ]);
    }

    /**
     * FareモデルがUserとのbelongsToリレーションを持つことを確認
     */
    public function test_fare_belongs_to_user()
    {
        $user = User::factory()->create();
        $fareRecord = $this->fareFactory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $fareRecord->user);
        $this->assertEquals($user->id, $fareRecord->user->id);
    }

    /**
     * Fareモデルのデータ属性にJSON形式の交通費データが格納できることを確認
     */
    public function test_fare_data_stores_json()
    {
        $fareData = [
            [
                'date' => '2024-06-11',
                'detail' => [
                    [
                        'traffic' => 'JR',
                        'fare' => 500,
                        'route' => '東京-品川',
                    ]
                ],
                'sub_total' => 500,
            ]
        ];

        $fareRecord = $this->fareFactory()->create([
            'data' => json_encode($fareData),
        ]);

        $decoded = json_decode($fareRecord->data, true);
        $this->assertIsArray($decoded);
        $this->assertEquals('2024-06-11', $decoded[0]['date']);
        $this->assertEquals('JR', $decoded[0]['detail'][0]['traffic']);
        $this->assertEquals(500, $decoded[0]['detail'][0]['fare']);
    }

    /**
     * Fareモデルの東京用合計フィールドが正しく保存できることを確認
     */
    public function test_fare_tokyo_total_fields()
    {
        $fareRecord = $this->fareFactory()->create([
            'first_total' => 15000,
            'second_total' => 8000,
        ]);

        $this->assertEquals(15000, $fareRecord->first_total);
        $this->assertEquals(8000, $fareRecord->second_total);
    }

    /**
     * Fareモデルの大阪用合計フィールドが正しく保存できることを確認
     */
    public function test_fare_osaka_total_field()
    {
        $fareRecord = $this->fareFactory()->create([
            'osaka_total' => 25000,
            'first_total' => null,
            'second_total' => null,
        ]);

        $this->assertEquals(25000, $fareRecord->osaka_total);
        $this->assertNull($fareRecord->first_total);
        $this->assertNull($fareRecord->second_total);
    }

    /**
     * Fareモデルの大阪用データ形式が正しく保存できることを確認
     */
    public function test_fare_osaka_data_format()
    {
        $osakaData = [
            [
                'date' => '2024-06-11',
                'traffic' => 'バス',
                'fare' => 300,
                'start' => '難波',
                'end' => '梅田',
                'way' => '片道',
                'route' => '市バス',
            ]
        ];

        $fareRecord = $this->fareFactory()->create([
            'data' => json_encode($osakaData),
            'osaka_total' => 300,
        ]);

        $decoded = json_decode($fareRecord->data, true);
        $this->assertIsArray($decoded);
        $this->assertEquals('バス', $decoded[0]['traffic']);
        $this->assertEquals('難波', $decoded[0]['start']);
        $this->assertEquals('梅田', $decoded[0]['end']);
    }

    /**
     * ユーザーが削除された場合、関連するFareも削除されることを確認（cascade）
     */
    public function test_fare_is_deleted_when_user_is_deleted()
    {
        $user = User::factory()->create();
        $fareRecord = $this->fareFactory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('fares', ['id' => $fareRecord->id]);

        $user->delete();

        $this->assertDatabaseMissing('fares', ['id' => $fareRecord->id]);
    }
}
