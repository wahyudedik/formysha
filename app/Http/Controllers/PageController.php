<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the "About Us" page.
     */
    public function about(): View
    {
        return view('pages.about');
    }

    /**
     * Display the "Privacy Policy" page.
     */
    public function privacy(): View
    {
        return view('pages.privacy');
    }

    /**
     * Display the "Terms & Conditions" page.
     */
    public function terms(): View
    {
        return view('pages.terms');
    }
}
