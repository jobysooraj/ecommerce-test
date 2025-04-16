<?php

namespace App\Repositories\Order;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    /**
     * Get all order items.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return OrderItem::all();
    }

    /**
     * Find an order item by its ID.
     *
     * @param  int  $id
     * @return OrderItem
     */
    public function find($id): OrderItem
    {
        return OrderItem::findOrFail($id);
    }

    /**
     * Create a new order item.
     *
     * @param  array  $data
     * @return OrderItem
     */
    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    /**
     * Update an existing order item.
     *
     * @param  int  $id
     * @param  array  $data
     * @return OrderItem
     */
    public function update($id, array $data): OrderItem
    {
        $orderItem = $this->find($id);
        $orderItem->update($data);

        return $orderItem;
    }

    /**
     * Delete an order item.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete($id): bool
    {
        $orderItem = $this->find($id);
        return $orderItem->delete();
    }
}
