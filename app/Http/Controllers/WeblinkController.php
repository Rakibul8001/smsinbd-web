<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class WeblinkController extends Controller
{
    function convertAlphanumericToNumber($alphanumeric)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($characters);
        $number = 0;
        
        $length = strlen($alphanumeric);
        for ($i = 0; $i < $length; $i++) {
            $char = $alphanumeric[$i];
            $index = strpos($characters, $char);
            $number = $number * $base + $index + 1;
        }

        return $number;
    }


    public function index($code){
        $number = $this->convertAlphanumericToNumber($code);

        $digitArr = str_split($number);

        return view('index',compact('number','digitArr'));
    }
}