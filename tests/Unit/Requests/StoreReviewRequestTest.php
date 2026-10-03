<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreReviewRequestTest extends TestCase
{
    private function getRules(): array
    {
        return (new StoreReviewRequest())->rules();
    }

    public function test_評価値が1から5の範囲内であれば通過すること()
    {
        $data = ['rating' => 3, 'comment' => 'テストコメント'];
        $validator = Validator::make($data, $this->getRules());
        $this->assertTrue($validator->passes());
    }

    public function test_評価値が範囲外の場合に失敗すること()
    {
        $validator = Validator::make(['rating' => 0], $this->getRules());
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['rating' => 6], $this->getRules());
        $this->assertTrue($validator->fails());
    }
}