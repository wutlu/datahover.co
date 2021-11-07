<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Etsetra\Library\DateTime;

use App\Models\Logs;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');
    }

    /**
     * Index
     * 
     * @return view
     */
    public function index(Request $request)
    {
        return view('home');
    }

    /**
     * Dashboard
     * 
     * @return view
     */
    public function dashboard()
    {
        return view('dashboard');
    }
}
