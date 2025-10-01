<?php

namespace App\Http\Controllers\Deposit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Api\Deposit\DepositStoreRequest;
use App\Http\Requests\Admin\Api\Deposit\DepositUpdateRequest;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function index(): \Inertia\Response
    {
        $deposit = Deposit::paginate(20);
        return Inertia::render('Deposit/Index', ['deposits' => $deposit]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Deposit/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepositStoreRequest $request)
    {
        Deposit::create($request->validated());
        return redirect()->route('deposits.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Deposit $deposit): \Inertia\Response
    {
        return Inertia::render('Deposit/Show', ['deposit' => $deposit]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepositUpdateRequest $request, Deposit $deposit): \Illuminate\Http\RedirectResponse
    {
        $deposit->update($request->validated());
        return redirect()->route('deposits.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit $deposit): void
    {
        $deposit->delete();

    }

    public function depositByOrderId($id)
    {
        $deposit = Deposit::query()->where('order_id', $id)->get();
        if ($deposit) {
            return response()->json(['deposit' => $deposit], 200);
        } else {
            return response()->json(['deposit' => null], 404);
        }
    }
}
