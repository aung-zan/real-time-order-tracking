<?php

namespace App\Http\Controllers;

use App\Repositories\DriverRepository;

class DriverController extends Controller
{
    private $driverRepository;
    private $header;
    private $errorContent;

    public function __construct(DriverRepository $driverRepository)
    {
        $this->driverRepository = $driverRepository;
        $this->header = [
            'Content-Type' => 'application/json',
        ];
        $this->errorContent = [
            'message' => 'something went wrong.',
        ];
    }

    /**
     * Get a driver's status via id.
     *
     * @param int $id
     */
    public function status(int $id)
    {
        try {
            $driver = $this->driverRepository->getById($id);

            $content = [
                'data' => $driver,
                'message' => 'retrieved successfully.'
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
