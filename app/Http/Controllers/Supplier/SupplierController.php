<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\SupplierStoreRequest;
use App\Http\Requests\Supplier\SupplierUpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::when($request->search, function ($query) use ($request) {
            $query
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        })->paginate(10)->withQueryString();
        return Inertia::render('Supplier/Index', ['suppliers' => $suppliers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Supplier/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierStoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        Supplier::create($request->all());
        return redirect()->route('suppliers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return Inertia::render('Supplier/Show', ['supplier' => $supplier]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return Inertia::render('Supplier/Edit', ['supplier' => $supplier]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        $supplier->update($request->all());
        return redirect()->route('suppliers.index');
    }

    public function purchases(Request $request, Supplier $supplier)
    {
        // Optional filters
        $filters = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'status' => 'nullable|in:unpaid,partial,paid',
            'search_note' => 'nullable|string|max:255',
        ]);

        $query = $supplier->purchases()
            ->select('id', 'supplier_id', 'date', 'total', 'paid_amount', 'due_amount', 'status', 'note')
            ->when($filters['from'] ?? null, fn($q, $v) => $q->whereDate('date', '>=', $v))
            ->when($filters['to'] ?? null, fn($q, $v) => $q->whereDate('date', '<=', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['search_note'] ?? null, fn($q, $v) => $q->where('note', 'like', "%{$v}%"))
            ->orderByDesc('date')
            ->orderByDesc('id');

        // Inertia page
        return Inertia::render('Supplier/Purchases', [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ],
            'filters' => $filters,
            'purchases' => $query->paginate(15)->withQueryString()
                ->through(fn($p) => [
                    'id' => $p->id,
                    'date' => $p->date,
                    'total' => (int)$p->total,
                    'paid_amount' => (int)$p->paid_amount,
                    'due_amount' => (int)$p->due_amount,
                    'status' => $p->status,
                    'note' => $p->note,
                    // handy links
                    'show_url' => route('purchases.show', $p->id), // if you have it
                    'pay_url' => route('payments.payForPurchasePage', $p->id), // from earlier
                ]),
        ]);
    }


}
