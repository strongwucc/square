<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function root()
    {
        echo phpinfo();
        // return view('pages.root');
    }
}
