<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function redirectToDashboard()
    {
        return redirect('dashboard');
        // return view('home');
    }

    public function index()
    {
        // return redirect('dashboard');
        return view('home');
    }



    public function chatbot(){

        return view('chatbot');
        
    }
}
