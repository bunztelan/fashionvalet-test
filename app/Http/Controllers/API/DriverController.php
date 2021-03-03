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
        //$driver = Driver::whereRaw('email LIKE "%fvtaxi%" or email LIKE "%fvdrive%" ')->get();

        $completedRides = DB::table('bookings')
            ->select('driver_id', DB::raw('count(id) as completed_rides'))
            ->where('state', '=', 'COMPLETED')->groupBy('driver_id');

        $cancelledRides = DB::table('bookings')
            ->select('driver_id', DB::raw('count(id) as cancelled_rides'))
            ->where('state', '!=', 'COMPLETED')->groupBy('driver_id');

        $unique_passenger = DB::table('bookings')
            ->select('driver_id', DB::raw('count(distinct passenger_id) as unique_passenger'))
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
            ->select('id', 'completed_rides', 'cancelled_rides', 'unique_passenger')
            ->where('completed_rides', '>', 2)
            ->get();

        // $data = DB::table('drivers')
        //     ->leftJoin('bookings', function ($join) {
        //         $join->on('drivers.id', '=', 'bookings.driver_id')->where('bookings.state', '=', "COMPLETED");
        //     })
        //     ->select('drivers.id', DB::raw('count(*)'))
        //     ->get();
        // ->select('id',DB::table('users'))
        // ->where(function ($query) {
        //     $query->where('drivers.email', 'LIKE', '%fvtaxi%')
        //         ->orWhere('drivers.email', 'LIKE', '%fvdrive%');
        // })
        // ->leftJoin(DB::raw("(SELECT COUNT(*) AS number_of_completed_rides FROM bookings b WHERE b.state = 'COMPLETED' GROUP BY b.driver_id)")
        // // ->leftJoin(DB::raw("(SELECT b.driver_id, COUNT(*) AS number_of_cancelled_rides FROM bookings b WHERE b.state LIKE 'CANCELLED%' GROUP BY b.driver_id) cancelled_bookings"), 'drivers.id', '=', 'cancelled_bookings.driver_id')
        // // ->leftJoin(DB::raw("(SELECT c.driver_id, COUNT(*) AS number_of_unique_passengers FROM (SELECT b.driver_id, b.passenger_id FROM bookings b WHERE b.state = 'COMPLETED' GROUP BY b.driver_id, b.passenger_id) c GROUP BY c.driver_id) unique_passengers"), 'drivers.driver_id', '=', 'unique_passengers.driver_id')
        // // ->leftJoin(DB::raw("(SELECT b.driver_id, SUM(b.fare) AS total_fare FROM bookings b WHERE b.state = 'COMPLETED' GROUP BY b.driver_id) completed_fare"), 'drivers.driver_id', '=', 'completed_fare.driver_id')
        // // ->selectRaw('drivers.driver_id AS driver_id, COALESCE(number_of_completed_rides, 0) AS number_of_completed_rides, COALESCE(number_of_cancelled_rides, 0) AS number_of_cancelled_rides, COALESCE(number_of_unique_passengers, 0) AS number_of_unique_passengers, COALESCE(total_fare, 0) AS total_fare, COALESCE(total_fare * 0.2, 0) AS total_commission')
        // ->where('number_of_completed_rides', '>', 10)
        // ->where('number_of_unique_passengers', '<', 5)
        // ->orderBy('number_of_completed_rides', 'DESC')
        // ->get();

        return ResponseFormatter::success(
            $data,
            "Report generated"
        );
    }
}
