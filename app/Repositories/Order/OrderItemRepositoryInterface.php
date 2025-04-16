<?php

namespace App\Repositories\Order;

use App\Models\OrderItem;

interface OrderItemRepositoryInterface
{
    public function all();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}
