<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DesaProfile extends Model
{
    protected $fillable = ['desa','puskesmas_id','faskes_terdekat','cakupan'];

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class);
    }
}
