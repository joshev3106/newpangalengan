<?php

namespace App\Providers;

use App\Models\Stunting;
use App\Models\Puskesmas;
use App\Observers\StuntingObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Stunting::observe(StuntingObserver::class);

        View::composer('component.header', function ($view) {
            // hitung data cepat (latest per desa)
            $latest = Stunting::select('desa', DB::raw('MAX(period) as period'))->groupBy('desa');
            $rows = Stunting::joinSub($latest, 'latest', function ($join) {
                    $join->on('stuntings.desa','=','latest.desa')
                         ->on('stuntings.period','=','latest.period');
                })
                ->get(['stuntings.desa','stuntings.kasus','stuntings.populasi']);
            
            $withRate = $rows->map(function($r){
                $rate = $r->populasi > 0 ? round(($r->kasus / $r->populasi) * 100, 1) : 0;
                $sev  = $rate > 20 ? 'high' : ($rate >= 10 ? 'medium' : ($rate > 0 ? 'low' : 'not'));
                return (object)['rate'=>$rate,'severity'=>$sev];
            });
        
            $stats = [
                'high'   => $withRate->where('severity','high')->count(),
                'medium' => $withRate->where('severity','medium')->count(),
                'low'    => $withRate->where('severity','low')->count(),
                'not'    => $withRate->where('severity','not')->count(),
                'total'  => $withRate->count(),
                'avg'    => round($withRate->avg(fn($x)=>$x->rate) ?? 0, 1),
            ];
        
            $pkCount = 0;
            try {
                $pkCount = Puskesmas::count();
            } catch (\Throwable $e) {
                $pkCount = count(config('desa_puskesmas.pk_coords', []));
            }
        
            $view->with('stats', $stats)->with('pkCount', $pkCount);
        });
    }
}
