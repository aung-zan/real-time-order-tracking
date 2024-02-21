<?php

namespace App\Repositories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class DriverRepository
{
    /**
     * raw query case for a driver's progress.
     *
     * @return string
     */
    private function caseRaw(): string
    {
        return "case
            when drivers.status = 1 then orders.progress
            when drivers.status = 0 then 0
        end as progress,";
    }

    /**
     * Raw query for get the lastest order by driver id.
     *
     * @return string
     */
    private function whereRaw(): string
    {
        return "created_at = (
            select max(created_at)
            from orders
            where driver_id = ?
        )";
    }

    /**
     * Return sub query of orders to join with drivers
     *
     * @param int $id
     * @return Builder
     */
    private function getLatestOrderByDriverId(int $id): Builder
    {
        $whereRawQuery = $this->whereRaw();

        $latestOrder = DB::table('orders')
            ->whereRaw($whereRawQuery, [$id]);

        return $latestOrder;
    }

    /**
     * Return the driver info via requested id.
     *
     * @param int $id
     * @return Collection
     */
    public function getById(int $id): Collection
    {
        $caseRawQuery = $this->caseRaw();
        $joinSubQuery = $this->getLatestOrderByDriverId($id);

        $driver = Driver::joinSub($joinSubQuery, 'orders', function (JoinClause $join) {
            $join->on('drivers.id', '=', 'orders.driver_id');
        })
            ->selectRaw($caseRawQuery . "drivers.*")
            ->where('drivers.id', $id)
            ->get();

        if ($driver->isEmpty()) {
            throw new ModelNotFoundException("Resource not found.", 404);
        }

        return $driver;
    }

    /**
     * Return a driver which status is 0 [free].
     *
     * @return Driver|null
     */
    public function getFreeDriver(): Driver | null
    {
        $driver = Driver::where('status', 0)
            ->first();

        return $driver;
    }

    /**
     * Return all drivers which status is 0 [free].
     *
     * @return Driver|Collection
     */
    public function getFreeDrivers(): Driver | Collection
    {
        $driver = Driver::where('status', 0)
            ->get();

        return $driver;
    }

    /**
     * change the driver status to 0 [Free].
     *
     * @return void
     */
    public function freeDriver(int $id): void
    {
        Driver::where('id', $id)
            ->update(['status' => 0]);
    }

    /**
     * Change the driver status to 1 [Shipping].
     *
     * @return void
     */
    public function shippingDriver(int $id): void
    {
        Driver::where('id', $id)
            ->update(['status' => 1]);
    }
}
