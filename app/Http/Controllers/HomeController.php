<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    //
    public function home()
    {
        Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'barcode']);
        Http::post('http://127.0.0.1:5002/stop_service', ['service' => 'assignment']);
        return view('home');
    }
}
