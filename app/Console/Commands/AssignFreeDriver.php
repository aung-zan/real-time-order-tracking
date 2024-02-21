<?php

namespace App\Console\Commands;

use App\Repositories\DriverRepository;
use App\Repositories\OrderRepository;
use Illuminate\Console\Command;

class AssignFreeDriver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AssignFreeDriver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $orderRepository;

    private $driverRepository;

    public function __construct(OrderRepository $orderRepository, DriverRepository $driverRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepository;
        $this->driverRepository = $driverRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderWithoutDriver = $this->orderRepository->getOrderWithoutDriver();

        if (! $orderWithoutDriver->isEmpty()) {
            $freeDrivers = $this->driverRepository->getFreeDrivers();

            if (! $freeDrivers->isEmpty()) {
                $freeDriversCount = $freeDrivers->count();
                $slicedOrders = $orderWithoutDriver->splice(0, $freeDriversCount);

                foreach ($slicedOrders as $key => $order) {
                    $driverID = $freeDrivers[$key]->id;
                    $this->orderRepository->assignOrderToFreeDriver($order, $driverID);
                }
            }
        }

        \Log::info('assign completed');
    }
}
