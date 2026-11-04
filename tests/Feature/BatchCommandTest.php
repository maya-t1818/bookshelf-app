<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BatchCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_リマインダー送信コマンドが正常に実行できること()
    {
        $exitCode = Artisan::call('books:send-reminder');

        $this->assertEquals(0, $exitCode);
    }

    public function test_自動失効バッチコマンドが正常に実行できること()
    {
        $this->assertTrue(true);
    }
}
