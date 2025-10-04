<?php

namespace App\Controller;

use App\Model\Order;
use App\Model\Member;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class OrderController
{
    public function index(Request $request, Response $response): Response
    {
        $orders = Order::with('member')->get();

        $result = [
            'status' => true,
            'message' => 'Successfully',
            'data' => $orders
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function store(Request $request, Response $response): Response
    {
        $post = $request->getParsedBody();

        $order = Order::create($post);

        $result = [
            'status' => (bool) $order,
            'message' => $order ? 'Order created successfully' : 'Failed to create order',
            'data' => $order
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function update(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $post = $request->getParsedBody();

        $orderId = $params['orderId'] ?? null;
        $order = Order::where('id', $orderId)->first();

        $order->update($post);

        $result = [
            'status' => true,
            'message' => 'Order updated successfully',
            'data' => $order
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $orderId = $params['orderId'] ?? null;

        $order = Order::where('id', $orderId)->first();
        $order->delete();

        $result = [
            'status' => true,
            'message' => 'Order deleted successfully',
            'data' => $order
        ];

        return JsonResponse::withJson($response, $result, 200);
    }
}
