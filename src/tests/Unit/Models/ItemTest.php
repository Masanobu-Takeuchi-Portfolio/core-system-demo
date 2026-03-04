<?php

namespace Tests\Unit\Models;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * fillable属性が正しく設定されていることを確認
     */
    public function test_fillable_attributes()
    {
        $item = new Item();
        $expected = [
            'user_id',
            'year',
            'month',
            'data',
            'notes',
        ];

        $this->assertEquals($expected, $item->getFillable());
    }

    /**
     * Itemモデルがファクトリで正常に作成できることを確認
     */
    public function test_item_can_be_created_with_factory()
    {
        $item = Item::factory()->create();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'year' => $item->year,
            'month' => $item->month,
        ]);
    }

    /**
     * ItemモデルがUserとのbelongsToリレーションを持つことを確認
     */
    public function test_item_belongs_to_user()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $item->user);
        $this->assertEquals($user->id, $item->user->id);
    }

    /**
     * Itemモデルのデータ属性にJSON形式の物品発注データが格納できることを確認
     */
    public function test_item_data_stores_json()
    {
        $itemData = [
            [
                'date' => '2024-06-11',
                'type' => 'ゴミ袋',
                'num' => 10,
                'destination' => '倉庫A',
                'deadline' => '2024-06-15',
                'note' => 'テスト備考',
            ]
        ];

        $item = Item::factory()->create([
            'data' => json_encode($itemData),
        ]);

        $decoded = json_decode($item->data, true);
        $this->assertIsArray($decoded);
        $this->assertEquals('2024-06-11', $decoded[0]['date']);
        $this->assertEquals('ゴミ袋', $decoded[0]['type']);
        $this->assertEquals(10, $decoded[0]['num']);
        $this->assertEquals('倉庫A', $decoded[0]['destination']);
    }

    /**
     * Itemモデルの備考欄が正しく保存できることを確認
     */
    public function test_item_notes_can_be_stored()
    {
        $item = Item::factory()->create([
            'notes' => '発注に関する備考テスト',
        ]);

        $this->assertEquals('発注に関する備考テスト', $item->notes);
    }

    /**
     * Itemモデルの備考欄がnullでも保存できることを確認
     */
    public function test_item_notes_can_be_null()
    {
        $item = Item::factory()->create([
            'notes' => null,
        ]);

        $this->assertNull($item->notes);
    }

    /**
     * Itemモデルで複数の物品データをJSON形式で保存できることを確認
     */
    public function test_item_multiple_data_entries()
    {
        $itemData = [
            [
                'date' => '2024-06-11',
                'type' => 'ゴミ袋',
                'num' => 10,
                'destination' => '倉庫A',
                'deadline' => '2024-06-15',
                'note' => '',
            ],
            [
                'date' => '2024-06-12',
                'type' => '洗剤',
                'num' => 5,
                'destination' => '倉庫B',
                'deadline' => '2024-06-16',
                'note' => '急ぎ',
            ],
        ];

        $item = Item::factory()->create([
            'data' => json_encode($itemData),
        ]);

        $decoded = json_decode($item->data, true);
        $this->assertCount(2, $decoded);
        $this->assertEquals('ゴミ袋', $decoded[0]['type']);
        $this->assertEquals('洗剤', $decoded[1]['type']);
    }

    /**
     * ユーザーが削除された場合、関連するItemも削除されることを確認（cascade）
     */
    public function test_item_is_deleted_when_user_is_deleted()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('items', ['id' => $item->id]);

        $user->delete();

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }
}
