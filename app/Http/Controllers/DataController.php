<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function home() {
        return view('home');
    }

    public function analisisHotspot() {
        return view('analisis-hotspot');
    }

    public function dataStunting() {
        return view('data-stunting');
    }

    public function peta() {
        return view('peta');
    }

    public function dataWilayah() {
        return view('data-wilayah');
    }

    public function laporan() {
        return view('laporan');
    }
