<?php

namespace App\Console\Commands;

use App\Events\OrderStatusUpdated;
use App\Repositories\OrderRepository;
use Illuminate\Console\Command;

class ChangeOrderProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChangeOrderProgress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = $this->orderRepository->getShippingOrders();

        foreach ($orders as $order) {
            $increasedProgress = $order->progress + 25;

            if ($increasedProgress === 100) {
                $this->orderRepository->completeOrder($order);
            } else {
                $this->orderRepository->changeOrderProgress($order, $increasedProgress);
            }

            OrderStatusUpdated::dispatch($order);
        }
    }
}
