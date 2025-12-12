<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->products();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $products = $query->orderBy('name')
            ->paginate($request->input('per_page', 10));

        return response()->json($products);
    }

    public function all(Request $request)
    {
        $products = $request->user()->products()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'hsn_code', 'unit', 'price', 'gst_rate']);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'hsn_code' => ['required', 'string', 'min:4', 'max:8', 'regex:/^\d{4,8}$/'],
            'unit' => ['required', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:0'],
            'gst_rate' => ['required', 'numeric', 'in:0,5,12,18,28'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = $request->user()->products()->create($validated);

        return response()->json($product, 201);
    }

    public function show(Request $request, Product $product)
    {
        $this->authorize('view', $product);

        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'hsn_code' => ['required', 'string', 'min:4', 'max:8', 'regex:/^\d{4,8}$/'],
            'unit' => ['required', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:0'],
            'gst_rate' => ['required', 'numeric', 'in:0,5,12,18,28'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'min_stock_level' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->authorize('delete', $product);

        // Check if product has any bill items
        if ($product->billItems()->exists()) {
            return response()->json([
                'message' => 'Cannot delete product that has been used in bills.',
            ], 422);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }
}
