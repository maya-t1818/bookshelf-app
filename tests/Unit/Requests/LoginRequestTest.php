<?php

namespace Tests\Unit\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールとパスワードが未入力の場合のエラーメッセージ()
    {
    $response = $this->post('/login', [
        'email'    => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_ログイン情報が不一致の場合のエラーメッセージ()
    {
        $response = $this->post('/login', [
            'email'    => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}