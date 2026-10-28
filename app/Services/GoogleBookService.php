<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GoogleBookService
{
    public function searchByIsbn(string $isbn): ?array
    {
        $apiKey = config('services.google_books.key');

        $queryParams = [
            'q' => "isbn:{$isbn}",
        ];

        if (! empty($apiKey)) {
            $queryParams['key'] = $apiKey;
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', $queryParams);

        if ($response->status() === 429) {
            throw new Exception('Google Books API のクォータを超過しました。.env に GOOGLE_BOOKS_API_KEY を設定してください。', 429);
        }

        if ($response->failed()) {
            throw new Exception('API通信エラーが発生しました。', 500);
        }

        $data = $response->json();

        if (empty($data['items'])) {
            return null;
        }

        $volumeInfo = $data['items'][0]['volumeInfo'] ?? [];

        return [
            'title' => $volumeInfo['title'] ?? '',
            'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : '',
            'published_date' => $volumeInfo['publishedDate'] ?? null,
            'description' => $volumeInfo['description'] ?? '',
            'image_url' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
        ];
    }
}
