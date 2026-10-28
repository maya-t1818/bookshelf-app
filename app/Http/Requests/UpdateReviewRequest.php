<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * エラーメッセージ
     *
     * @return array{"comment.max": string, "comment.required": string, "rating.required": string}
     */
    public function messages(): array
    {
        return [
            'rating.required' => '評価は必須です。',
            'comment.required' => 'コメントは必須です。',
            'comment.max' => 'コメントは1000文字以内で入力してください。',
        ];
    }
}
