<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン画面が表示できること()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_ユーザー登録画面が表示できること()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_ログイン済みユーザーはログイン画面にアクセスするとリダイレクトされること()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect();
    }
}