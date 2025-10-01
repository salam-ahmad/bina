<?php

namespace App\Http\Controllers\Welcome;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index(): \Inertia\Response
    {
        $setting = Setting::first();
        return Inertia::render('Welcome/Welcome', ['setting' => $setting]);
    }
}
