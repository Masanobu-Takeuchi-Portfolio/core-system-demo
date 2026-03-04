<?php

namespace Tests\Unit\Models;

use App\Models\Owner;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ownerモデルが認証可能（Authenticatable）であることを確認
     */
    public function test_owner_is_authenticatable()
    {
        $owner = new Owner();
        $this->assertInstanceOf(Authenticatable::class, $owner);
    }

    /**
     * fillable属性が正しく設定されていることを確認
     */
    public function test_fillable_attributes()
    {
        $owner = new Owner();
        $expected = [
            'name',
            'email',
            'password',
        ];

        $this->assertEquals($expected, $owner->getFillable());
    }

    /**
     * hidden属性が正しく設定されていることを確認
     */
    public function test_hidden_attributes()
    {
        $owner = new Owner();
        $expected = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($expected, $owner->getHidden());
    }

    /**
     * casts属性が正しく設定されていることを確認
     */
    public function test_casts_attributes()
    {
        $owner = new Owner();
        $casts = $owner->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }

    /**
     * Ownerモデルがファクトリで正常に作成できることを確認
     */
    public function test_owner_can_be_created_with_factory()
    {
        $owner = Owner::factory()->create();

        $this->assertDatabaseHas('owners', [
            'id' => $owner->id,
            'email' => $owner->email,
        ]);
    }

    /**
     * Ownerモデルのテーブル名がownersであることを確認
     */
    public function test_owner_table_name()
    {
        $owner = new Owner();
        $this->assertEquals('owners', $owner->getTable());
    }

    /**
     * Ownerモデルでパスワードがhidden属性として返されないことを確認
     */
    public function test_owner_password_is_hidden_in_array()
    {
        $owner = Owner::factory()->create();
        $array = $owner->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }
}
