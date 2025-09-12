<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    // public function home() {
    //     return view('home.index');
    // }

    // public function analisisHotspot() {
    //     return view('analisis-hotspot');
    // }

    // public function dataStunting() {
    //     return view('data-stunting');
    // }

    public function peta(Request $request) {
        $tab = $request->query('tab', 'stunting'); // default ke 'stunting'
        // jaga-jaga kalau value aneh
        if (!in_array($tab, ['stunting','puskesmas'], true)) $tab = 'stunting';
        return view('peta.index', compact('tab'));
    }

    // public function dataWilayah() {
    //     return view('data-wilayah');
    // }

    public function laporan() {
        return view('laporan.index');
    }
}