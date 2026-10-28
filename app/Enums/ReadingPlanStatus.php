<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case Planned = 'planned';    // 計画中
    case InProgress = 'in_progress';    // 読書中
    case Completed = 'completed';    // 読了

    /**
     * 日本語のラベルを返す
     */
    public function label(): string
    {
        return match ($this) {
            self::Planned => '計画中',
            self::InProgress => '読書中',
            self::Completed => '読了',
        };
    }

    /**
     * ステータスに応じたTailwindのCSSクラスを返す
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Planned => 'bg-gray-100 text-gray-800',
            self::InProgress => 'bg-yellow-100 text-yellow-800',
            self::Completed => 'bg-green-100 text-green-800',
        };
    }
}
