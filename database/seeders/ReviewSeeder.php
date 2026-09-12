<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        if ($users->count() < 5 || $books->count() < 11) {
            return;
        }

        $userList = $users->values();
        $bookList = $books->values();

        $reviewsData = [
            ['book_idx' => 0, 'user_idx' => 1, 'rating' => 5, 'comment' => 'ユーモア溢れる語り口で、何年経っても古さを感じさせない名作です。'],
            ['book_idx' => 0, 'user_idx' => 2, 'rating' => 4, 'comment' => '文章表現がとても独特で引き込まれました。猫の目線からの人間風刺が秀逸です。'],
            ['book_idx' => 0, 'user_idx' => 3, 'rating' => 3, 'comment' => '旧仮名遣いや言い回しに少し苦戦しましたが、後半は楽しく読めました。'],

            ['book_idx' => 1, 'user_idx' => 0, 'rating' => 5, 'comment' => '仕事でもプライベートでも使える人間関係の原則が詰まっています。何度も読み返したい。'],
            ['book_idx' => 1, 'user_idx' => 2, 'rating' => 5, 'comment' => '相手の立場に立つことの大切さを再認識させられました。全ての社会人におすすめです。'],
            ['book_idx' => 1, 'user_idx' => 4, 'rating' => 4, 'comment' => '事例が豊富でわかりやすいですが、実践するのはなかなか難しいと感じました。'],

            ['book_idx' => 2, 'user_idx' => 1, 'rating' => 5, 'comment' => '変数名や関数の切り分けなど、明日からのコーディングですぐに使える知見ばかりでした。'],
            ['book_idx' => 2, 'user_idx' => 3, 'rating' => 5, 'comment' => 'プログラマーとしての基本姿勢を学べる名著です。新人研修の必読書にしたい。'],
            ['book_idx' => 2, 'user_idx' => 4, 'rating' => 4, 'comment' => '薄い本ですが内容はとても濃いです。定期的に見返して振り返りたいです。'],

            ['book_idx' => 3, 'user_idx' => 0, 'rating' => 5, 'comment' => '人生の指針となる本。第1章のパラダイムシフトから一気に引き込まれました。'],
            ['book_idx' => 3, 'user_idx' => 2, 'rating' => 4, 'comment' => '分量が多く読むのに時間がかかりましたが、それだけの価値がある素晴らしい一冊でした。'],
            ['book_idx' => 3, 'user_idx' => 4, 'rating' => 5, 'comment' => '「重要事項を優先する」という原則を意識するだけで、日々の行動が変わりました。'],

            ['book_idx' => 4, 'user_idx' => 1, 'rating' => 4, 'comment' => '勢いのあるストーリー展開でテンポよく読めました。主人公の破天荒さが心地よいです。'],
            ['book_idx' => 4, 'user_idx' => 3, 'rating' => 3, 'comment' => '短編なので読みやすかったです。昔の言葉遣いですが、正義感あふれる言動が印象的。'],

            ['book_idx' => 5, 'user_idx' => 0, 'rating' => 5, 'comment' => '人類の歴史観が一変する衝撃的な内容でした。認知革命の話が特に面白かったです。'],
            ['book_idx' => 5, 'user_idx' => 2, 'rating' => 5, 'comment' => '巨大なスケールで歴史を紐解いていて知的興奮が止まりませんでした。'],
            ['book_idx' => 5, 'user_idx' => 4, 'rating' => 4, 'comment' => '上巻下巻ともに読み応えがあります。人類の未来について深く考えさせられました。'],

            ['book_idx' => 6, 'user_idx' => 1, 'rating' => 5, 'comment' => 'リファクタリングの手法やテストコードの重要性が実践的に学べます。'],
            ['book_idx' => 6, 'user_idx' => 3, 'rating' => 4, 'comment' => 'Javaベースのコード例ですが、他の言語でも十分に応用できる本質的な内容でした。'],
            ['book_idx' => 6, 'user_idx' => 4, 'rating' => 4, 'comment' => '綺麗で読みやすいコードを書く意識が格段に高まりました。買って良かったです。'],

            ['book_idx' => 7, 'user_idx' => 0, 'rating' => 5, 'comment' => 'アドラー心理学の「課題の分離」という考え方を知り、人間関係の悩みが軽くなりました。'],
            ['book_idx' => 7, 'user_idx' => 1, 'rating' => 4, 'comment' => '対話形式なのでスラスラ読めます。自分らしく生きる勇気をもらえました。'],
            ['book_idx' => 7, 'user_idx' => 3, 'rating' => 5, 'comment' => '他人の期待に応える必要はないという言葉にとても救われた気持ちになりました。'],

            ['book_idx' => 8, 'user_idx' => 2, 'rating' => 4, 'comment' => 'お笑いに情熱を注ぐ芸人たちの葛藤と青春が繊細に描かれています。ラストシーンが印象的。'],
            ['book_idx' => 8, 'user_idx' => 3, 'rating' => 3, 'comment' => '独特の文章表現で少し好みが分かれるかもしれませんが、芸人の世界のリアリティが伝わります。'],
            ['book_idx' => 8, 'user_idx' => 4, 'rating' => 4, 'comment' => '熱量のある素晴らしい小説でした。挫折と情熱のコントラストが見事です。'],

            ['book_idx' => 9, 'user_idx' => 0, 'rating' => 5, 'comment' => 'データに基づいて世界を見る大切さを実感。自分の思い込みに気付かされました。'],
            ['book_idx' => 9, 'user_idx' => 1, 'rating' => 5, 'comment' => 'クイズ形式から始まり、楽しみながら読み進められます。世界は少しずつ良くなっている。'],
            ['book_idx' => 9, 'user_idx' => 2, 'rating' => 4, 'comment' => 'ニュースの報道がいかに偏っているかを考えさせられる一冊です。全人類に読んでほしい。'],

            ['book_idx' => 10, 'user_idx' => 0, 'rating' => 4, 'comment' => 'ただの「四角い箱」が世界貿易の形を根本から変えたという歴史に引き込まれました。'],
            ['book_idx' => 10, 'user_idx' => 2, 'rating' => 4, 'comment' => '標準化とイノベーションの重要性を学べるビジネス書としても非常に有益でした。'],
            ['book_idx' => 10, 'user_idx' => 3, 'rating' => 3, 'comment' => '経済史の側面が強いので少し堅めですが、物流やビジネスに興味がある人にはかなり面白いです。'],
        ];

        foreach ($reviewsData as $data) {
            Review::create([
                'book_id' => $bookList[$data['book_idx']]->id,
                'user_id' => $userList[$data['user_idx']]->id,
                'rating'  => $data['rating'],
                'comment' => $data['comment'],
            ]);
        }
    }
}
