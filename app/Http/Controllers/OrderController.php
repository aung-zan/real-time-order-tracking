<?php

namespace App\Http\Controllers;

use App\Events\OrderShipped;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    private $orderRepository;
    private $header;
    private $errorContent;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->header = [
            'Content-Type' => 'application/json',
        ];
        $this->errorContent = [
            'message' => 'something went wrong.',
        ];
    }

    /**
     * Store an order in db.
     */
    public function store()
    {
        try {
            $order = $this->orderRepository->create();

            $content = [
                'data' => $order,
                'message' => 'retrieved successfully.',
            ];
            return response()->json($content, 200, $this->header);
        } catch (\Throwable $th) {
            \Log::info($th);
            if ($th->getCode() !== 0) {
                $content = ['message' => $th->getMessage()];
                return response()->json($content, $th->getCode(), $this->header);
            }

            return response()->json($this->errorContent, 500, $this->header);
        }
    }

    /**
     * Get an order's status via id.
     *
     * @param int $id
     */
    public function status(int $id)
    {
        try {
            $order = $this->orderRepository->getById($id);

            $content = [
                'data' => $order,
                'message' => 'retireved successfully.',
            ];
            return response()->json($content, 200, $this->header);
        } catch (\Throwable $th) {
            if ($th->getCode() !== 0) {
                $content = ['message' => $th->getMessage()];
                return response()->json($content, $th->getCode(), $this->header);
            }

            return response()->json($this->errorContent, 500, $this->header);
        }
    }

    /**
     * Cancel an order via id.
     *
     * @param int $id
     */
    public function cancel(int $id)
    {
        try {
            $this->orderRepository->cancelOrder($id);

            $content = [
                'message' => 'the order cancellation is successfully.',
            ];
            return response()->json($content, 200, $this->header);
        } catch (\Throwable $th) {
            \Log::info($th);
            if ($th->getCode() !== 0) {
                $content = ['message' => $th->getMessage()];
                return response()->json($content, $th->getCode(), $this->header);
            }

            return response()->json($this->errorContent, 500, $this->header);
        }
    }
}
