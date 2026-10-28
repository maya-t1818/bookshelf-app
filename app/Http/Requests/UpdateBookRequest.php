<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $book = $this->route('book');
        $bookId = $book instanceof Book ? $book->id : $book;

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['required',
                'string',
                'digits:13',
                Rule::unique('books', 'isbn')->ignore($bookId)],
            'published_date' => ['nullable', 'date'],
            'genres' => ['nullable', 'array', 'min:1'],
            'genres.*' => ['exists:genres,id'],
            'image_url' => ['nullable', 'url'],
        ];
    }

    /**
     * エラーメッセージ
     *
     * @return array{"author.max": string, "genres.min": string, "image_url.max": string, "image_url.url": string, "isbn.digits": string, "isbn.required": string, "isbn.unique": string, "published_date.date": string, "title.max": string, "title.required": string}
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'author.max' => '著者名は255文字以内で入力してください。',
            'isbn.required' => 'ISBNは必須です。',
            'isbn.digits' => 'ISBNは13桁で入力してください。',
            'isbn.unique' => 'そのISBNは既に使用されています。',
            'published_date.date' => '出版日は有効な日付形式で入力してください。',
            'genres.min' => 'ジャンルは1つ以上選択してください。',
            'image_url.url' => '画像URLは有効なURL形式で入力してください。',
            'image_url.max' => '画像URLは255文字以内で入力してください。',
        ];
    }
}
