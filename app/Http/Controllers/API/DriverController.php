<?php

namespace App\Http\Controllers\API;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DriverController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');

        if ($id) {
            $driver = Driver::find($id);
            if ($driver) {
                return ResponseFormatter::success(
                    $driver,
                    'Successfully loaded driver data',
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Driver not found',
                    404
                );
            }
        }
        $driver = Driver::query();
        return ResponseFormatter::success(
            $driver->paginate($limit),
            'Driver data loaded'
        );
    }

    public function getAnswer()
    {
        $completedRides = DB::table('bookings')
            ->select('driver_id', DB::raw('count(id) as completed_rides'))
            ->where('state', '=', 'COMPLETED')->groupBy('driver_id')->get();

        $cancelledRides = DB::table('bookings')
            ->select('driver_id', DB::raw('count(id) as cancelled_rides'))
            ->where('state', '!=', 'COMPLETED')->groupBy('driver_id');

        $unique_passenger = DB::table('bookings')
            ->select('driver_id', DB::raw('count(distinct passenger_id) as unique_passenger'))
            ->where('state', '=', 'COMPLETED')->groupBy('driver_id')->get();


        return ResponseFormatter::success(
            $unique_passenger,
            "Report generated"
        );
    }
}
