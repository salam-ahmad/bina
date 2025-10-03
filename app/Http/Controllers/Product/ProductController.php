<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with(['unit','currency'])->where('name', 'like', '%' . $request->input('search') . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Product/Index', ['products' => $products]);
    }

    public function create(): \Inertia\Response
    {
        $units = Unit::all();
        $currencies = Currency::all();
        return Inertia::render('Product/Create', ['units' => $units, 'currencies' => $currencies]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        Product::create($request->all());
        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): \Inertia\Response
    {
        return Inertia::render('Product/Show', ['product' => $product]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): \Inertia\Response
    {
        return Inertia::render('Product/Edit', ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $product->update($request->all());
        return redirect()->route('products.index');
    }


}
