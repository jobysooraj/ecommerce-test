<?php

namespace App\Services\Order;

use App\Repositories\Order\OrderItemRepositoryInterface;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;

class OrderItemService
{
    protected $orderItemRepo;

    public function __construct(OrderItemRepositoryInterface $orderItemRepo)
    {
        $this->orderItemRepo = $orderItemRepo;
    }

    /**
     * Create a new order item for a specific order.
     *
     * @param array $data
     * @return OrderItem
     */
    public function createOrderItem(array $data): OrderItem
    {
        // Ensure the order exists
        $order = Order::findOrFail($data['order_id']);
        // Ensure the product exists and has sufficient stock
        $product = Product::findOrFail($data['product_id']);

        if ($product->stock < $data['quantity']) {
            throw new \Exception("Not enough stock for product {$product->name}");
        }

        // Calculate the total for the order item
        $total = $data['quantity'] * $data['price'];

        // Prepare data for the new order item
        $orderItemData = [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'total' => $total,
        ];

        // Create the order item
        $orderItem = $this->orderItemRepo->create($orderItemData);

        // Update stock in the product model
        $product->decrement('stock', $data['quantity']);

        // Update the total amount in the order
        $this->updateOrderTotalAmount($order);

        return $orderItem;
    }

    /**
     * Update an existing order item.
     *
     * @param int $id
     * @param array $data
     * @return OrderItem
     */
    public function updateOrderItem(int $id, array $data): OrderItem
    {
        // Ensure the order item exists
        $orderItem = $this->orderItemRepo->find($id);
        // Ensure the order exists
        $order = Order::findOrFail($orderItem->order_id);
        // Ensure the product exists and has sufficient stock
        $product = Product::findOrFail($data['product_id']);

        if ($product->stock < $data['quantity']) {
            throw new \Exception("Not enough stock for product {$product->name}");
        }

        // Calculate total for the order item
        $total = $data['quantity'] * $data['price'];

        // Prepare updated data for the order item
        $updatedData = [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'total' => $total,
        ];

        // Update the order item
        $orderItem = $this->orderItemRepo->update($id, $updatedData);

        // Update stock in the product model
        $product->decrement('stock', $data['quantity'] - $orderItem->quantity); // Adjust stock based on the update

        // Update the total amount in the order
        $this->updateOrderTotalAmount($order);

        return $orderItem;
    }

    /**
     * Delete an order item and restore stock in the product.
     *
     * @param int $id
     * @return bool
     */
    public function deleteOrderItem(int $id): bool
    {
        // Ensure the order item exists
        $orderItem = $this->orderItemRepo->find($id);
        // Retrieve the product
        $product = Product::findOrFail($orderItem->product_id);
        // Restore the stock to the product
        $product->increment('stock', $orderItem->quantity);

        // Delete the order item
        $orderItem->delete();

        // Retrieve the associated order and update total amount
        $order = Order::findOrFail($orderItem->order_id);
        $this->updateOrderTotalAmount($order);

        return true;
    }

    /**
     * Recalculate and update the total amount for an order.
     *
     * @param Order $order
     * @return void
     */
    protected function updateOrderTotalAmount(Order $order): void
    {
        $totalAmount = 0;

        // Sum up the total of all order items
        foreach ($order->orderItems as $orderItem) {
            $totalAmount += $orderItem->total;
        }

        // Update the order's total amount
        $order->update(['total_amount' => $totalAmount]);
    }
}
