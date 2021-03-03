<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    //
    public function submitAddress(Request $request)
    {
        $address_1 = $request->input("address_1");
        $address_2 = $request->input("address_2");
        $address_3 = $request->input('address_3');
    }
}
