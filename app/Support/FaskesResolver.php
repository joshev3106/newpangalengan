<?php

namespace App\Support;

use App\Models\Puskesmas;

class FaskesResolver
{
    /**
     * Return: ['puskesmas_id' => int|null, 'faskes_text' => string|null]
     */
    public static function resolveForDesa(string $desa): array
    {
        $desa = trim($desa);
        $map  = config('desa_puskesmas', []);

        // 1) Mapping manual
        if (!empty($map[$desa])) {
            $pk = Puskesmas::where('nama', $map[$desa])->first();
            if ($pk) return ['puskesmas_id' => $pk->id, 'faskes_text' => null];
            return ['puskesmas_id' => null, 'faskes_text' => $map[$desa]];
        }

        // 2) Nama puskesmas mengandung nama desa
        $match = Puskesmas::where('nama', 'like', '%'.$desa.'%')->first();
        if ($match) {
            return ['puskesmas_id' => $match->id, 'faskes_text' => null];
        }

        // 3) Fallback "Puskesmas {Desa}"
        $namaBersih = preg_replace('/^(desa|kelurahan)\s+/i', '', $desa);
        return ['puskesmas_id' => null, 'faskes_text' => "Puskesmas {$namaBersih}"];
    }
}
