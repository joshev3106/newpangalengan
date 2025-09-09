<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Stunting extends Model
{
    protected $fillable = ['desa','kasus','populasi','period'];
    protected $casts   = ['period' => 'date'];

    // Rate dihitung dinamis (tidak disimpan)
    public function getRateAttribute(): float {
        return $this->populasi > 0 ? round(($this->kasus / $this->populasi) * 100, 1) : 0.0;
    }

    public function getSeverityAttribute(): string {
        $r = $this->rate;
        if ($r > 20)       return 'high';
        if ($r >= 10)      return 'medium';
        return 'low';
    }

    // --- Scopes filter untuk index() ---
    public function scopeSearch(Builder $q, ?string $key): Builder {
        return $key ? $q->where('desa','like',"%{$key}%") : $q;
    }
    public function scopeSeverity(Builder $q, ?string $sev): Builder {
        if (!$sev) return $q;
        // filter berdasarkan rate
        if ($sev === 'high')   return $q->whereRaw('populasi > 0 AND (kasus / populasi) * 100 > 20');
        if ($sev === 'medium') return $q->whereRaw('populasi > 0 AND (kasus / populasi) * 100 BETWEEN 10 AND 20');
        if ($sev === 'low')    return $q->whereRaw('populasi > 0 AND (kasus / populasi) * 100 < 10');
        return $q;
    }
    public function scopePeriodBetween(Builder $q, ?string $from, ?string $to): Builder {
        // from/to format "YYYY-MM"
        if ($from) $q->whereDate('period','>=', Carbon::createFromFormat('Y-m',$from)->startOfMonth());
        if ($to)   $q->whereDate('period','<=', Carbon::createFromFormat('Y-m',$to)->endOfMonth());
        return $q;
    }
}
