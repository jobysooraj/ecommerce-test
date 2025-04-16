<?php

namespace App\Services\Order;

use App\Repositories\Order\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    protected $orderRepo;

    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    /**
     * Create a new order with associated order items.
     *
     * @param array $data
     * @return Order
     */
    public function getAllOrders()
    {
        return Order::with('orderItems')->get(); // Fetch all orders with their associated order items
    }
    public function getById($id)
    {
        return $this->orderRepo->find($id);
    }

    public function createOrder(array $data): Order
    {
        $orderData = [
            'user_id' => Auth::id(),
            'order_date' => $data['order_date'],
            'status' => $data['status'],
            'total_amount' => 0, // This will be calculated later
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'],
        ];

        // Create the order
        $order = $this->orderRepo->create($orderData);

        $totalAmount = 0;
        foreach ($data['order_items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                throw new \Exception("Not enough stock for product {$product->name}");
            }

            // Create order item
            $orderItemData = [
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ];

            OrderItem::create($orderItemData);

            // Update the total amount
            $totalAmount += $orderItemData['total'];

            // Deduct stock from the product
            $product->decrement('stock', $item['quantity']);
        }

        // Update the total amount in the order
        $order->update(['total_amount' => $totalAmount]);

        return $order;
    }

    /**
     * Update an existing order's details and order items.
     *
     * @param int $orderId
     * @param array $data
     * @return Order
     */
    public function updateOrder(int $orderId, array $data): Order
    {
        $order = $this->orderRepo->find($orderId);

        // Update the order data
        $order->update([
            'status' => $data['status'],
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'],
        ]);

        // Recalculate the total amount (if needed)
        $totalAmount = 0;
        foreach ($data['order_items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                throw new \Exception("Not enough stock for product {$product->name}");
            }

            // Update order item
            $orderItemData = [
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ];

            // Check if the order item exists
            $orderItem = OrderItem::where('order_id', $order->id)
                                  ->where('product_id', $item['product_id'])
                                  ->first();

            if ($orderItem) {
                $orderItem->update($orderItemData);
            } else {
                // Create a new order item if it doesn't exist
                OrderItem::create($orderItemData);
            }

            // Update the total amount
            $totalAmount += $orderItemData['total'];

            // Deduct stock from the product
            $product->decrement('stock', $item['quantity']);
        }

        // Update the total amount in the order
        $order->update(['total_amount' => $totalAmount]);

        return $order;
    }

    /**
     * Delete an existing order and its associated order items.
     *
     * @param int $orderId
     * @return bool
     */
    public function deleteOrder(int $orderId): bool
    {
        $order = $this->orderRepo->find($orderId);
        foreach ($order->orderItems as $orderItem) {
            $product = Product::find($orderItem->product_id);
            $product->increment('stock', $orderItem->quantity);
            $orderItem->delete();
        }

        return $order->delete();
    }
}
