<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * fillable属性が正しく設定されていることを確認
     */
    public function test_fillable_attributes()
    {
        $user = new User();
        $expected = [
            'name_last',
            'name_first',
            'staff_number',
            'department_id',
            'email',
            'password',
            'url_transportation_expenses',
            'url_schedule',
            'dummy',
        ];

        $this->assertEquals($expected, $user->getFillable());
    }

    /**
     * hidden属性が正しく設定されていることを確認
     */
    public function test_hidden_attributes()
    {
        $user = new User();
        $expected = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($expected, $user->getHidden());
    }

    /**
     * casts属性が正しく設定されていることを確認
     */
    public function test_casts_attributes()
    {
        $user = new User();
        $casts = $user->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }

    /**
     * Userモデルがファクトリで正常に作成できることを確認
     */
    public function test_user_can_be_created_with_factory()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * scopeSearch: 姓（name_last）で検索できることを確認
     */
    public function test_scope_search_by_name_last()
    {
        User::factory()->create(['name_last' => '田中']);
        User::factory()->create(['name_last' => '佐藤']);
        User::factory()->create(['name_last' => '鈴木']);

        $request = new Request(['search_name_last' => '田中']);
        $result = User::search($request)->get();

        $this->assertCount(1, $result);
        $this->assertEquals('田中', $result->first()->name_last);
    }

    /**
     * scopeSearch: 名（name_first）で検索できることを確認
     */
    public function test_scope_search_by_name_first()
    {
        User::factory()->create(['name_first' => '太郎']);
        User::factory()->create(['name_first' => '花子']);

        $request = new Request(['search_name_first' => '太郎']);
        $result = User::search($request)->get();

        $this->assertCount(1, $result);
        $this->assertEquals('太郎', $result->first()->name_first);
    }

    /**
     * scopeSearch: メールアドレスで検索できることを確認
     */
    public function test_scope_search_by_email()
    {
        User::factory()->create(['email' => 'tanaka@example.com']);
        User::factory()->create(['email' => 'sato@example.com']);

        $request = new Request(['search_email' => 'tanaka']);
        $result = User::search($request)->get();

        $this->assertCount(1, $result);
        $this->assertEquals('tanaka@example.com', $result->first()->email);
    }

    /**
     * scopeSearch: 複数条件で検索できることを確認
     */
    public function test_scope_search_by_multiple_conditions()
    {
        User::factory()->create([
            'name_last' => '田中',
            'name_first' => '太郎',
            'email' => 'tanaka.taro@example.com',
        ]);
        User::factory()->create([
            'name_last' => '田中',
            'name_first' => '花子',
            'email' => 'tanaka.hanako@example.com',
        ]);

        $request = new Request([
            'search_name_last' => '田中',
            'search_name_first' => '太郎',
        ]);
        $result = User::search($request)->get();

        $this->assertCount(1, $result);
        $this->assertEquals('太郎', $result->first()->name_first);
    }

    /**
     * scopeSearch: 検索条件なしの場合は全件取得されることを確認
     */
    public function test_scope_search_without_conditions_returns_all()
    {
        User::factory()->count(3)->create();

        $request = new Request();
        $result = User::search($request)->get();

        $this->assertCount(3, $result);
    }

    /**
     * scopeSearch: 全角スペースを含む検索ワードで検索できることを確認
     */
    public function test_scope_search_with_fullwidth_space()
    {
        User::factory()->create(['name_last' => '田中']);

        // 全角スペースを含む検索ワード
        $request = new Request(['search_name_last' => '　田中　']);
        $result = User::search($request)->get();

        $this->assertCount(1, $result);
    }

    /**
     * scopeSearch: 部分一致で検索できることを確認
     */
    public function test_scope_search_partial_match()
    {
        User::factory()->create(['name_last' => '田中太郎']);

        $request = new Request(['search_name_last' => '田中']);
        $result = User::search($request)->get();

        $this->assertCount(1, $result);
    }

    /**
     * scopeSearch: 該当なしの場合は空のコレクションが返ることを確認
     */
    public function test_scope_search_no_match_returns_empty()
    {
        User::factory()->create(['name_last' => '田中']);

        $request = new Request(['search_name_last' => '山田']);
        $result = User::search($request)->get();

        $this->assertCount(0, $result);
    }
}
