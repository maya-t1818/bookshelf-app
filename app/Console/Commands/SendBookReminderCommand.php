<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendBookReminderCommand extends Command
{
    protected $signature = 'books:send-reminder';

    protected $description = 'ユーザーに未読本の読書リマインダーを送信する';

    public function handle(): int
    {
        User::has('books')->get()->each(function ($user) {

            Log::info("リマインダー送信: {$user->email}");
        });

        $this->info('読書リマインダーの送信が完了しました。');

        return Command::SUCCESS;
    }
}
