<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StuntingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Form kamu kirim "YYYY-MM" → kita normalisasi ke tgl 1
        $period = $this->input('period');
        if ($period) {
            try {
                $period = Carbon::parse($period.'-01')->startOfMonth()->format('Y-m-d');
                $this->merge(['period' => $period]);
            } catch (\Throwable $e) {}
        }

        $id = $this->route('stunting')?->id;

        return [
            'desa'     => ['required','string','max:100'],
            'kasus'    => ['required','integer','min:0'],
            'populasi' => ['required','integer','min:1'],
            'period'   => [
                'required','date',
                // unik per (desa,period); abaikan diri sendiri saat update
                Rule::unique('stuntings')->where(fn($q) =>
                    $q->where('desa', $this->desa)->where('period', $this->period)
                )->ignore($id),
            ],
        ];
    }
}
