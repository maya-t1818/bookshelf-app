<?php

namespace Tests\Unit\Requests;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_未入力や不整合時に指定したカスタムメッセージが返ること()
    {
        $action = new CreateNewUser();

        try {
            $action->create([
                'name'                  => '',
                'email'                 => 'invalid-email',
                'password'              => '1234567', 
                'password_confirmation' => '1234568', 
            ]);
            $this->fail('ValidationExceptionが発生しませんでした');
        } catch (ValidationException $e) {
            $errors = $e->errors();

            $this->assertEquals('お名前を入力してください', $errors['name'][0]);
            $this->assertEquals('メールアドレスはメール形式で入力してください', $errors['email'][0]);
            $this->assertEquals('パスワードは8文字以上で入力してください', $errors['password'][0]);
        }
    }
}