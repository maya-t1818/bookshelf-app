<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        $commentsByRating = [
            5 => [
                '素晴らしい本でした！',
                '人生が変わりました。',
                '何度も読み返しています。',
            ],
            4 => [
                'とても参考になりました。',
                '読みやすくておすすめです。',
                '期待通りの内容でした。',
            ],
            3 => [
                '普通でした。',
                '可もなく不可もなく。',
                '期待したほどではなかった。',
            ],
            2 => [
                '少し期待外れでした。',
                '内容が薄い印象。',
                'もう少し深掘りしてほしかった。',
            ],
            1 => [
                '残念ながら合いませんでした。',
                '期待と違いました。',
            ],
        ];

        foreach ($books as $book) {
            // 各書籍に2〜4件のレビュー件数をランダムに決定
            $reviewCount = rand(2, 4);

            // 投稿者を重複しないようにランダムに選出
            $targetUsers = $users->random(min($reviewCount, $users->count()));

            foreach ($targetUsers as $user) {
                // 1〜5の評価をランダムに決定
                $rating = rand(1, 5);

                // 決定した評価に対応するテンプレートからランダムにコメントを選択
                $comments = $commentsByRating[$rating];
                $comment = $comments[array_rand($comments)];

                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
            }
        }
    }
}
