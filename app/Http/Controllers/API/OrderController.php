<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Order\OrderService;
use App\Services\Order\OrderItemService;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;
    protected $orderItemService;

    public function __construct(OrderService $orderService, OrderItemService $orderItemService)
    {
        $this->orderService = $orderService;
        $this->orderItemService = $orderItemService;
    }
    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return response()->json($orders);
    }
    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $order = $this->orderService->createOrder($data);
            $orderDetails=$this->orderService->getById($order->id);

            DB::commit(); // Commit the transaction
            return response()->json(['message'=>'Order Placed','order' => $orderDetails], 201); // Return the created order with a 201 status
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if an error occurs
            return response()->json(['error' => $e->getMessage()], 400); // Handle error if stock is insufficient or any other issue
        }
    }
    public function show($id)
    {
        try {
            $order = $this->orderService->getById($id); // Fetch the order using the service method
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404); // Return 404 if order is not found
            }

            $responseData = [
                'order_id' => $order->id,
                'user_name' => $order->user ? $order->user->name : 'N/A',
                'order_date' => $order->order_date,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'shipping_address' => $order->shipping_address,
                'billing_address' => $order->billing_address,
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_name' => $item->product->name ?? 'N/A',
                        'product_price' => $item->price,
                        'purchase_quantity' => $item->quantity,
                        'product_stock'=>$item->product->stock,
                        'price' => $item->total,
                    ];
                }),
            ];

            return response()->json($responseData);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving order'], 500); // Return 500 for other errors
        }
    }
    public function update(OrderRequest $request, $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $order = $this->orderService->updateOrder($id, $data); // Update order details
            DB::commit();
            return response()->json(['order' => $order]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400); // Handle errors like stock issues or invalid data
        }
    }
    public function destroy($id)
    {

        DB::beginTransaction();
        try {
        $order=$this->orderService->deleteOrder($id);

            DB::commit();
            return response()->json(['message' => 'Order deleted successfully'], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400); // Handle deletion errors
        }
    }
    public function getUserOrders(Request $request)
    {
        try {
            $user = Auth::user();

            // Fetch all orders associated with the authenticated user
            $orders = $user->orders;

            if ($orders->isEmpty()) {
                return response()->json(['error' => 'No orders found for the user'], 404);
            }

            // Store the user's name once
            $userName = $user->name;

            $responseData = $orders->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'order_date' => $order->order_date,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'shipping_address' => $order->shipping_address,
                    'billing_address' => $order->billing_address,
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'product_name' => $item->product->name ?? 'N/A',
                            'product_price' => $item->price,
                            'purchase_quantity' => $item->quantity,
                            'product_stock' => $item->product->stock,
                            'price' => $item->total,
                        ];
                    }),
                ];
            });

            return response()->json([
                'user_name' => $userName, // Include the user's name once
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving orders'], 500);
        }
    }
}
