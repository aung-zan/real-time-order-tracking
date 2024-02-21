<?php

namespace App\Repositories;

use App\Models\Order;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    private $driverRepository;

    public function __construct(DriverRepository $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    /**
     * Create an order.
     *
     * @return Order $order
     */
    public function create(): Order
    {
        $order = DB::transaction(function () {
            $orderData = [
                'driver_id' => null,
                'progress' => 0,
                'status' => 0,
            ];

            $freeDriver = $this->driverRepository->getFreeDriver();
            if ($freeDriver) {
                $orderData['driver_id'] = $freeDriver->id;
                $orderData['status'] = 1;
                $this->driverRepository->shippingDriver($freeDriver->id);
            }

            return Order::create($orderData);
        });

        return $order;
    }

    /**
     * Get an order by id.
     *
     * @param int $id
     * @return Order $order
     */
    public function getById(int $id): Order
    {
        $order = Order::find($id);

        if (is_null($order)) {
            throw new ModelNotFoundException("Resource not found.", 404);
        }

        return $order;
    }

    /**
     * Get shipping orders
     *
     * @return Collection
     */
    public function getShippingOrders(): Collection
    {
        $orders = Order::where('status', 1)
            ->get();

        return $orders;
    }

    /**
     * Get orders that does not have driver.
     *
     * @return Collection
     */
    public function getOrderWithoutDriver(): Collection
    {
        $orderWithoutDriver = Order::where('driver_id', null)
            ->get();

        return $orderWithoutDriver;
    }

    /**
     * Assign order to a free driver.
     *
     * @param Order $order
     * @param int $driverID
     * @return void
     */
    public function assignOrderToFreeDriver(Order $order, int $driverID): void
    {
        DB::transaction(function () use ($order, $driverID) {
            $order->driver_id = $driverID;
            $order->status = 1;
            $order->save();
            $this->driverRepository->shippingDriver($driverID);

            \Log::info('order id: ' . $order->id);
            \Log::info('driver id: ' . $driverID);
        });
    }

    /**
     * Change the shipping order progress.
     *
     * @param Order $order
     * @param int $progress
     * @return void
     */
    public function changeOrderProgress(Order $order, int $progress): void
    {
        $order->progress = $progress;
        $order->save();

        \Log::info('order id: ' . $order->id);
        \Log::info('progress: ' . $progress);
    }

    /**
     * Cancel an order by id.
     *
     * @param int $id
     */
    public function cancelOrder(int $id): void
    {
        $order = $this->getById($id);

        if ($order->status === "completed") {
            throw new Exception("This order cannot be cancelled.", 409);
        }

        DB::transaction(function () use ($order) {
            $order->status = 3;
            $order->progress = 0;
            $order->save();

            $this->driverRepository->freeDriver($order->driver_id);
        });
    }

    /**
     * Complete an order by id.
     *
     * @param Order $order
     */
    public function completeOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->status = 2;
            $order->progress = 100;
            $order->save();

            $this->driverRepository->freeDriver($order->driver_id);

            \Log::info('order id: ' . $order->id);
            \Log::info('progress: 100');
        });
    }
}
