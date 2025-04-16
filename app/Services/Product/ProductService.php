<?php
namespace App\Services\Product;

use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductService
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function getAll()
    {
        return $this->productRepo->all();
    }

    public function getById($id)
    {
        return $this->productRepo->find($id);
    }

    public function create(array $data)
    {
        $data['code'] = 'P' . strtoupper(Str::random(6));
        $data['slug'] = Product::generateUniqueSlug($data['name']);

        return $this->productRepo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->productRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->productRepo->delete($id);
    }
}
