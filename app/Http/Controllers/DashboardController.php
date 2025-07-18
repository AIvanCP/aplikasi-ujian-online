<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard');
    }

    public function faq()
    {
        return Inertia::render('faq/faq-page');
    }
}
