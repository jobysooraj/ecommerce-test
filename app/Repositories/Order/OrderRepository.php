<?php

namespace App\Repositories\Order;

use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Get all orders.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Order[]
     */
    public function all()
    {
        return Order::all();
    }

    /**
     * Find an order by its ID.
     *
     * @param int $id
     * @return Order
     */
    public function find($id)
    {
        return Order::with('user','orderItems')->findOrFail($id);
    }

    /**
     * Create a new order.
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data)
    {
        return Order::create($data);
    }

    /**
     * Update an existing order.
     *
     * @param int $id
     * @param array $data
     * @return Order
     */
    public function update($id, array $data)
    {
        $order = Order::findOrFail($id);
        $order->update($data);
        return $order;
    }

    /**
     * Delete an order by its ID.
     *
     * @param int $id
     * @return bool|null
     */
    public function delete($id)
    {
        $order = Order::where('status','pending')->findOrFail($id);

        return $order->delete();
    }
}
