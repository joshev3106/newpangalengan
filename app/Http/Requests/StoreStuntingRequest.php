<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStuntingRequest extends FormRequest {
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void
    {
        if ($this->filled('period')) {
            // Normalisasi ke tgl 1
            $this->merge([
                'period' => Carbon::createFromFormat('Y-m', $this->input('period'))
                            ->startOfMonth()->format('Y-m-d'),
            ]);
        }
    }
    public function rules(): array {
        $desaOptions = array_keys(config('desa_coords', []));
        return [
            'desa'     => ['required','string','max:100', Rule::in($desaOptions)],
            'kasus'    => ['required','integer','min:0'],
            'populasi' => ['required','integer','min:1'],
            'period'   => ['required','date_format:Y-m-d', Rule::unique('stuntings')->where(fn($q) => $q->where('desa', $this->desa)),
        ],
        ];
    }

    public function messages(): array
    {
        return [
            'period.unique' => 'Data untuk desa tersebut pada bulan ini sudah ada.',
        ];
    }
}
