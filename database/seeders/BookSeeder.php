<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            return;
        }

        $books = [
            [
                'number' => 1,
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_date' => '1905-01-01',
                'description' => '夏目漱石の長編小説であり、デビュー作。猫の視点から人間模様を風刺的に描いた名作。',
                'genres' => ['小説'],
            ],
            [
                'number' => 2,
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'isbn' => '9784422100524',
                'published_date' => '1936-10-01',
                'description' => 'あらゆる人間関係の悩みを解決するための原則が書かれた、歴史的名著。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'number' => 3,
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'isbn' => '9784873115658',
                'published_date' => '2012-06-23',
                'description' => 'より良いコードを書くためのシンプルで実践的なテクニックを満載したプログラマー必読の一冊。',
                'genres' => ['技術書'],
            ],
            [
                'number' => 4,
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'isbn' => '9784863940246',
                'published_date' => '2013-08-30',
                'description' => '成功への架け橋となる7つの原則を解説。全世界でロングセラーを誇る生き方のバイブル。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'number' => 5,
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'isbn' => '9784101010021',
                'published_date' => '1906-04-01',
                'description' => '四国の旧制中学校に赴任した正義感あふれる江戸っ子教師の暴れっぷりを描くユーモア小説。',
                'genres' => ['小説'],
            ],
            [
                'number' => 6,
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712',
                'published_date' => '2016-09-08',
                'description' => 'ホモ・サピエンスがなぜ地球の支配者になれたのかを圧倒的なスケールで解き明かす一冊。',
                'genres' => ['歴史', '科学'],
            ],
            [
                'number' => 7,
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9784048930598',
                'published_date' => '2017-12-18',
                'description' => 'アジャイルソフトウェア達人による、プログラミングの美学とリファクタリングの実践ガイド。',
                'genres' => ['技術書'],
            ],
            [
                'number' => 8,
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'isbn' => '9784478025819',
                'published_date' => '2013-12-13',
                'description' => 'アルフレッド・アドラーの思想（アドラー心理学）を、青年と哲人の対話篇形式で解き明かす。',
                'genres' => ['自己啓発'],
            ],
            [
                'number' => 9,
                'title' => '火花',
                'author' => '又吉直樹',
                'isbn' => '9784163902302',
                'published_date' => '2015-03-11',
                'description' => '売れない芸人・徳永と、天才肌の先輩芸人・神谷。お笑いの世界で葛藤する男たちの青春文学。第153回芥川賞受賞作。',
                'genres' => ['小説'],
            ],
            [
                'number' => 10,
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289607',
                'published_date' => '2019-01-11',
                'description' => 'ファクトに基づいて世界を正しく見る習慣。データに基づき世界を正しく捉える重要性を説く。',
                'genres' => ['ビジネス', '科学'],
            ],
            [
                'number' => 11,
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822251468',
                'published_date' => '2007-01-18',
                'description' => '「四角い箱」である貨物コンテナの発明が、いかに世界経済と物流に革命をもたらしたかを描くノンフィクション。',
                'genres' => ['ビジネス', '歴史'],
            ],
        ];

        foreach ($books as $book) {
            
            $book = Book::firstOrCreate(
                ['isbn' => $book['isbn']], 
                [
                    'user_id' => $user->id,
                    'title' => $book['title'],
                    'author' => $book['author'],
                    'published_date' => $book['published_date'],
                    'description' => $book['description'],
                    'image_url' => "https://placehold.co/200x300/e2e8f0/475569?text={$book['number']}",
                ]
            );

            $genreIds = Genre::whereIn('name', $book['genres'])->pluck('id');
            $book->genres()->sync($genreIds);
        }
    }
}
