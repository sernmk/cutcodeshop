<?php

declare(strict_types = 1);

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;

final class TelegramBotApi
{
    public const HOST = 'https://api.telegram.org/bot';

    public static function sendMessage(string $token, int $chatId, string $text): bool
    {
        try {
            Http::get(self::HOST . $token . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text
            ]);
        } catch (\Exception $exception) {
            return false;
        }
        return true;

    }
}
