<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Services\Product\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index()
    {
        return response()->json($this->productService->getAll());

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product = $this->productService->create($data);
            DB::commit();
            return response()->json([
                'message' => 'Product created successfully.',
                'product' => $product,
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            // Delete image if uploaded before the exception
            if (!empty($data['image']) && Storage::disk('public')->exists($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }
            return response()->json([
                'message' => 'Failed to create product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json($this->productService->getById($id));
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $product = $this->productService->getById($id);

            $data = $request->only(['name', 'price', 'stock', 'description', 'is_active']);

            if ($request->hasFile('image')) {
                if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $updatedProduct = $this->productService->update($id, $data);

            DB::commit();

            return response()->json([
                'message' => 'Product updated successfully.',
                'product' => $updatedProduct,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($data['image']) && Storage::disk('public')->exists($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }

            return response()->json([
                'message' => 'Failed to update product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->productService->delete($id);
        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
