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
        // count completed rides
        $completedRides = DB::table('bookings')
            ->select('driver_id', DB::raw('count(id) as completed_rides'))
            ->where('state', '=', 'COMPLETED')->groupBy('driver_id');

        // count cancelled rides
        $cancelledRides = DB::table('bookings')
            ->select('driver_id', DB::raw('count(id) as cancelled_rides'))
            ->where('state', '!=', 'COMPLETED')->groupBy('driver_id');

        // count unique completed passenger
        $unique_passenger = DB::table('bookings')
            ->select('driver_id', DB::raw('count(distinct passenger_id) as unique_passenger'))
            ->where('state', '=', 'COMPLETED')->groupBy('driver_id');

        // count total fare 
        $total_fare = DB::table('bookings')
            ->select('driver_id', DB::raw('SUM(fare) as total_fare'), DB::raw('SUM(fare)*0.2 as total_commission'))
            ->where('state', '=', 'COMPLETED')->groupBy('driver_id');

        $data = DB::table('drivers')
            ->joinSub($completedRides, 'completed_rides', function ($join) {
                $join->on('drivers.id', '=', 'completed_rides.driver_id');
            })
            ->joinSub($cancelledRides, 'cancelled_rides', function ($join) {
                $join->on('drivers.id', '=', 'cancelled_rides.driver_id');
            })
            ->joinSub($unique_passenger, 'unique_passenger', function ($join) {
                $join->on('drivers.id', '=', 'unique_passenger.driver_id');
            })
            ->joinSub($total_fare, 'total_fare', function ($join) {
                $join->on('drivers.id', '=', 'total_fare.driver_id');
            })
            ->select('id', 'completed_rides', 'cancelled_rides', 'unique_passenger', 'total_fare', 'total_commission')
            ->where('completed_rides', '>', 10)
            ->where('unique_passenger', '<', 5)
            ->orderBy('completed_rides', 'DESC')
            ->get();

        return ResponseFormatter::success(
            $data,
            "Report generated"
        );
    }
}
