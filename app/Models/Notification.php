<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    // 主キーがインクリメント（連番）ではないことを指定
    public $incrementing = false;

    // 主キーの型が string (UUID) であることを指定
    protected $keyType = 'string';
}
