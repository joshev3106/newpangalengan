<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStuntingRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'desa'     => ['required','string','max:100'],
            'kasus'    => ['required','integer','min:0'],
            'populasi' => ['required','integer','min:1'],
            'period'   => ['required','date_format:Y-m'], // input bulan
        ];
    }
}
