<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingCreateRequest;
use App\Http\Requests\Setting\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $setting = Setting::all();
        return Inertia::render('Setting/Index', ['setting' => $setting]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Setting/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SettingCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        Setting::create($request->all());
        return redirect()->route('settings.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting): \Inertia\Response
    {
        return Inertia::render('Setting/Show', ['setting' => $setting]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting): \Inertia\Response
    {
        return Inertia::render('Setting/Edit', ['setting' => $setting]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingUpdateRequest $request, Setting $setting): \Illuminate\Http\RedirectResponse
    {
        $setting->update($request->all());
        return redirect()->route('settings.index');
    }
}
