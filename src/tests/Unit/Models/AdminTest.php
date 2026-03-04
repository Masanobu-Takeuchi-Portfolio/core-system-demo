<?php

namespace Tests\Unit\Models;

use App\Models\Admin;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Adminモデルが認証可能（Authenticatable）であることを確認
     */
    public function test_admin_is_authenticatable()
    {
        $admin = new Admin();
        $this->assertInstanceOf(Authenticatable::class, $admin);
    }

    /**
     * fillable属性が正しく設定されていることを確認
     */
    public function test_fillable_attributes()
    {
        $admin = new Admin();
        $expected = [
            'name',
            'email',
            'password',
        ];

        $this->assertEquals($expected, $admin->getFillable());
    }

    /**
     * hidden属性が正しく設定されていることを確認
     */
    public function test_hidden_attributes()
    {
        $admin = new Admin();
        $expected = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($expected, $admin->getHidden());
    }

    /**
     * casts属性が正しく設定されていることを確認
     */
    public function test_casts_attributes()
    {
        $admin = new Admin();
        $casts = $admin->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }

    /**
     * Adminモデルがファクトリで正常に作成できることを確認
     */
    public function test_admin_can_be_created_with_factory()
    {
        $admin = Admin::factory()->create();

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'email' => $admin->email,
        ]);
    }

    /**
     * Adminモデルのテーブル名がadminsであることを確認
     */
    public function test_admin_table_name()
    {
        $admin = new Admin();
        $this->assertEquals('admins', $admin->getTable());
    }

    /**
     * Adminモデルでパスワードがhidden属性として返されないことを確認
     */
    public function test_admin_password_is_hidden_in_array()
    {
        $admin = Admin::factory()->create();
        $array = $admin->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }
}
