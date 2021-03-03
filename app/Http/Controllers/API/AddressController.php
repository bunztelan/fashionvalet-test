<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    //
    public function submitAddress(Request $request)
    {
        //retrieve input request
        $address_1 = $request->input("address_1");
        $address_2 = $request->input("address_2");
        $address_3 = $request->input('address_3');

        //validate null input
        if ($address_1 == null && $address_2 == null && $address_3 == null) {
            return ResponseFormatter::error(
                null,
                "Please insert address",
                403
            );
        }
        // validate if all input is exist and each not more than 30
        if ($this->validateAddress($address_1) && $this->validateAddress($address_2) && $this->validateAddress($address_3)) {
            return ResponseFormatter::success(
                null,
                "Address is correct",
            );
        }

        // validate one of the address is empty
        // combine all addresses separated with space 
        $address = $address_1 . " " . $address_2 . " " . $address_3;
        //validate if combined address have more than 90 char
        if (strlen($address) > 90) {
            return ResponseFormatter::error(
                $address,
                "exceed address maximum length",
                400,
            );
        }

        return ResponseFormatter::success(
            $this->formatAddress($address),
            "Address formatted"
        );
    }

    function validateAddress($address)
    {
        $length = strlen($address);
        if ($length > 0 && $length < 30) {
            return true;
        }
        return false;
    }

    function formatAddress($addresses)
    {
        $formattedAddress = ['', '', ''];
        $arrayAddress = array_filter(explode(" ", $addresses));
        $index = 1;
        $addressIndex = 0;
        $next = $arrayAddress[$index];
        //        Business Office, Malcolm Long 92911 
        foreach ($arrayAddress as $address) {
            $formattedAddress[$addressIndex] .= $address;

            // + 1 because we need to seperate the word with space
            if ((strlen($formattedAddress[$addressIndex]) + 1 + strlen($next)) > 30 || ((count($arrayAddress)) - $index) < 3) {
                $addressIndex++;
            } else {
                $formattedAddress[$addressIndex] .= " ";
            }

            // check for the end of array
            if ($index + 1 < count($arrayAddress)) {
                $index++;
                $next = $arrayAddress[$index];
            }
        }
        return $formattedAddress;
    }
}
