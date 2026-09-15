<?php

namespace App\Http\Requests\api\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book')->id ?? $this->route('book');

        return [
            'title'        => ['required', 'string', 'max:255'],
            'author'       => ['required', 'string', 'max:255'],
            'isbn'         => ['required',
                'string',
                'digits:13',
                Rule::unique('books', 'isbn'),
            ],
            'published_at' => ['required', 'date'],
            'genre_ids'    => ['required', 'array', 'min:1'],
            'genre_ids.*'  => ['exists:genres,id'],
            'image_url'    => ['nullable', 'url'],
            'user_id'      => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'タイトルは必須です。',
            'title.max'             => 'タイトルは255文字以内で入力してください。',
            'author.required'       => '著者名は必須です。',
            'author.max'            => '著者名は255文字以内で入力してください。',
            'isbn.required'         => 'ISBNは必須です。',
            'isbn.digits'           => 'ISBNは13桁で入力してください。',
            'isbn.unique'           => 'そのISBNは既に使用されています。',
            'published_at.required' => '出版日は必須です。',
            'published_at.date'     => '出版日は有効な日付形式で入力してください。',
            'genre_ids.required'    => 'ジャンルは必須です。',
            'genre_ids.min'         => 'ジャンルは1つ以上選択してください。',
            'image_url.url'         => '画像URLは有効なURL形式で入力してください。',
            'image_url.max'         => '画像URLは255文字以内で入力してください。',
            'user_id.required'      => '登録者IDは必須です。',
        ];
    }
}
