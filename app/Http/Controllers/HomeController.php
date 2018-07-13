<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getHome(){
        $title = 'Inicio Place to Pay';
        return view('index', compact('title'));
    }
}
