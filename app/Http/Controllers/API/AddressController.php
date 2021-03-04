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
                400
            );
        }
        // validate if all input is already meet DHL standard
        if (
            $this->validateAddress($address_1) &&
            $this->validateAddress($address_2) &&
            $this->validateAddress($address_3)
        ) {
            return ResponseFormatter::success(
                null,
                "Address is correct",
            );
        }

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
        /**
         * validate at least combined address have minimum 3 words,
         * because every address need to be filled by at least 1 word
         */
        if (count(array_filter(explode(" ", $address))) < 3) {
            return ResponseFormatter::error(
                $address,
                "Address at least have 3 words",
                400,
            );
        }

        // generate formatted address
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

        foreach ($arrayAddress as $address) {

            $formattedAddress[$addressIndex] .= $address;
            // + 1 because we need to seperate the word with space and check for max length
            if ((strlen($formattedAddress[$addressIndex]) + 1 + strlen($next)) > 30 ||
                (((count($arrayAddress)) - $index) < 3 && strlen($formattedAddress[1]) < 1) ||
                (((count($arrayAddress)) - $index) < 3 && strlen($formattedAddress[2]) < 1)
            ) {
                $addressIndex++;
            }

            $formattedAddress[$addressIndex] .= " ";

            // check for the end of array
            if ($index + 1 < count($arrayAddress)) {
                $index++;
                $next = $arrayAddress[$index];
            }
        }
        return $formattedAddress;
    }
}
