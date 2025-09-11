<?php

namespace App\Observers;

use App\Models\Stunting;
use App\Models\DesaProfile;
use App\Support\FaskesResolver;

class StuntingObserver
{
    public function created(Stunting $st): void
    {
        $profile = DesaProfile::firstOrNew(['desa' => $st->desa]);

        if (empty($profile->puskesmas_id) && empty($profile->faskes_terdekat)) {
            $res = FaskesResolver::resolveForDesa($st->desa);
            $profile->puskesmas_id    = $res['puskesmas_id'];
            $profile->faskes_terdekat = $res['faskes_text'];
            $profile->save();
        }
    }
}
