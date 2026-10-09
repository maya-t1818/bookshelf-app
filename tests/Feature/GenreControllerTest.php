<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @dataProvider unauthenticatedRouteProvider
    */
    public function test_未認証ユーザーは各種画面や処理にアクセスできずログイン画面へリダイレクトされること(string $method, string $routeName, bool $needModel = false)
    {
        $genre = $needModel ? Genre::factory()->create() : null;
        $params = $genre ? ['genre' => $genre->id] : [];

        $response = $this->$method(route($routeName, $params), ['name' => 'テスト']);

        $response->assertRedirect(route('login'));
    }

    public static function unauthenticatedRouteProvider(): array
    {
        return [
            '一覧画面'   => ['get', 'genres.index'],
            '詳細画面'   => ['get', 'genres.show', true],
            '作成画面'   => ['get', 'genres.create'],
            '保存処理'   => ['post', 'genres.store'],
            '編集画面'   => ['get', 'genres.edit', true],
            '更新処理'   => ['put', 'genres.update', true],
            '削除処理'   => ['delete', 'genres.destroy', true],
        ];
    }



    public function test_ログイン済みユーザーはジャンル一覧画面を表示できること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertStatus(200);
        $response->assertViewIs('genres.index');
        $response->assertSee($genre->name);
    }

    public function test_ログイン済みユーザーはジャンル詳細画面を表示できること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $genre->books()->attach($book);

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
        $response->assertViewIs('genres.show');
        $response->assertSee($book->title);
    }

    public function test_ログイン済みユーザーはジャンル作成画面を表示できること()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.create'));

        $response->assertStatus(200);
        $response->assertViewIs('genres.create');
    }

    public function test_ログイン済みユーザーはジャンルを新規作成できること()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('genres.store'), [
            'name' => 'ミステリー',
        ]);

        $response->assertRedirect(route('genres.index'));
        $response->assertSessionHas('status', 'ジャンルを作成しました。');

        $this->assertDatabaseHas('genres', [
            'name' => 'ミステリー',
        ]);
    }

    public function test_作成した本人はジャンル編集画面を表示できること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.edit', $genre));

        $response->assertStatus(200);
        $response->assertViewIs('genres.edit');
    }

    public function test_作成した本人はジャンルを更新できること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '旧ジャンル名']);

        $response = $this->actingAs($user)->put(route('genres.update', $genre), [
            'name' => '新ジャンル名',
        ]);

        $response->assertRedirect(route('genres.index'));
        $response->assertSessionHas('status', 'ジャンルを更新しました。');

        $this->assertDatabaseHas('genres', [
            'id'   => $genre->id,
            'name' => '新ジャンル名',
        ]);
    }

    public function test_作成した本人は書籍が紐付いていないジャンルを削除できること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect(route('genres.index'));
        $response->assertSessionHas('status', 'ジャンルを削除しました。');

        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_書籍が紐付いているジャンルは削除できずエラーになること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $genre->books()->attach($book);

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertSessionHasErrors(['error' => 'このジャンルには書籍が紐付いているため削除できません。']);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }
}