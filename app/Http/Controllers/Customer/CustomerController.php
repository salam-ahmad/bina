<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerCreateRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $customers = Customer::where('name', 'like', '%' . $request->search . '%')
            ->orWhere('phone', 'like', '%' . $request->search . '%')
            ->paginate(10)->withQueryString();
        return Inertia::render('Customer/Index', ['customers' => $customers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Customer/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        Customer::create($request->all());
        return redirect()->route('customers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): \Inertia\Response
    {
        return Inertia::render('Customer/Show', ['customer' => $customer]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): \Inertia\Response
    {
        return Inertia::render('Customer/Edit', ['customer' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerUpdateRequest $request, Customer $customer): \Illuminate\Http\RedirectResponse
    {
        $customer->update($request->all());
        return redirect()->route('customers.index');
    }

    public function orders(Request $request, Customer $customer)
    {
        $filters = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'status' => 'nullable|in:unpaid,partial,paid',
            'search_note' => 'nullable|string|max:255',
        ]);

        $query = $customer->orders()
            ->select('id', 'customer_id', 'date', 'total', 'paid_amount', 'due_amount', 'status', 'note')
            ->when($filters['from'] ?? null, fn($q, $v) => $q->whereDate('date', '>=', $v))
            ->when($filters['to'] ?? null, fn($q, $v) => $q->whereDate('date', '<=', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['search_note'] ?? null, fn($q, $v) => $q->where('note', 'like', "%{$v}%"))
            ->orderByDesc('date')
            ->orderByDesc('id');

        return Inertia::render('Customer/Orders', [
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
            ],
            'filters' => $filters,
            'orders' => $query->paginate(15)->withQueryString()
                ->through(fn($o) => [
                    'id' => $o->id,
                    'date' => $o->date,
                    'total' => (int)$o->total,
                    'paid_amount' => (int)$o->paid_amount,
                    'due_amount' => (int)$o->due_amount,
                    'status' => $o->status,
                    'note' => $o->note,
                    // handy links
                    'show_url' => route('orders.show', $o->id),
                    'receive_url' => route('payments.receiveForOrderPage', $o->id),
                ]),
        ]);

    }

}
