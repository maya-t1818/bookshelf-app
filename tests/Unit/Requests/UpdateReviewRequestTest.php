<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateReviewRequestTest extends TestCase
{
    private function getRules(): array
    {
        return (new UpdateReviewRequest())->rules();
    }

    public function test_レビュー更新テスト()
    {
        $data = ['rating' => 5, 'comment' => '修正後のコメント'];
        $validator = Validator::make($data, $this->getRules());
        $this->assertTrue($validator->passes());
    }
}