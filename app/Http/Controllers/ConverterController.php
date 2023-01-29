<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class ConverterController extends Controller
{
    public function index(Request $request)
    {
        if (is_numeric($request->answer)) {
            $data['conversion'] = $this->convertToWords($request->answer);
        } else {
            $data['conversion'] = $this->convertToNumbers($request->answer);
        }

        $data['currency'] = is_numeric($data['conversion']) ? $data['conversion'] : $this->convertToNumbers($data['conversion']);

        $data['currency'] = $this->convertUsingAPI($data['currency']);

        return view('welcome', $data);
    }

    /**
     * @param string $val - string that is in numeric format
     *
     * @return string
     */
    private function convertToWords(string $val)
    {
        $ones = array(
            1 => "one",
            2 => "two",
            3 => "three",
            4 => "four",
            5 => "five",
            6 => "six",
            7 => "seven",
            8 => "eight",
            9 => "nine",
            10 => "ten",
            11 => "eleven",
            12 => "twelve",
            13 => "thirteen",
            14 => "fourteen",
            15 => "fifteen",
            16 => "sixteen",
            17 => "seventeen",
            18 => "eighteen",
            19 => "nineteen"
        );
        $tens = array(
            1 => "ten",
            2 => "twenty",
            3 => "thirty",
            4 => "forty",
            5 => "fifty",
            6 => "sixty",
            7 => "seventy",
            8 => "eighty",
            9 => "ninety"
        );
        $hundreds = array(
            "hundred",
            "thousand",
            "million",
            "billion",
            "trillion",
            "quadrillion"
        );

        $num = (float) $val;
        $num = number_format($num, 2, ".", ",");
        $num_arr = explode(".", $num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",", $wholenum));
        krsort($whole_arr);
        $rettxt = "";
        foreach ($whole_arr as $key => $i) {
            if ($i < 20) {
                $rettxt .= $ones[$i];
            } elseif ($i < 100) {
                $rettxt .= $tens[substr($i, 0, 1)];
                $rettxt .= " " . $ones[substr($i, 1, 1)];
            } else {
                $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                $rettxt .= " " . $tens[substr($i, 1, 1)];
                $rettxt .= " " . $ones[substr($i, 2, 1)];
            }
            if ($key > 0) {
                $rettxt .= " " . $hundreds[$key] . " ";
            }
        }
        if ($decnum > 0) {
            $rettxt .= " and ";
            if ($decnum < 20) {
                $rettxt .= $ones[$decnum];
            } elseif ($decnum < 100) {
                $rettxt .= $tens[substr($decnum, 0, 1)];
                $rettxt .= " " . $ones[substr($decnum, 1, 1)];
            }
        }
        return $rettxt;
    }

    /**
     * @param string $val - string that is in aphabet format
     *
     * @return int
     */
    private function convertToNumbers(string $val): int
    {

        // convert corresponding text to symbols (copied from internet didnt want to type the whole array too time consuming).
        $val = strtolower($val);
        $data = strtr(
            $val,
            array(
                'one'       => '1',
                'two'       => '2',
                'three'     => '3',
                'four'      => '4',
                'five'      => '5',
                'six'       => '6',
                'seven'     => '7',
                'eight'     => '8',
                'nine'      => '9',
                'ten'       => '10',
                'eleven'    => '11',
                'twelve'    => '12',
                'thirteen'  => '13',
                'fourteen'  => '14',
                'fifteen'   => '15',
                'sixteen'   => '16',
                'seventeen' => '17',
                'eighteen'  => '18',
                'nineteen'  => '19',
                'twenty'    => '20',
                'thirty'    => '30',
                'forty'     => '40',
                'fifty'     => '50',
                'sixty'     => '60',
                'seventy'   => '70',
                'eighty'    => '80',
                'ninety'    => '90',
                'hundred'   => '100',
                'thousand'  => '1000',
                'million'   => '1000000',
                'billion'   => '1000000000',
                'trillion'   => '1000000000000',
                'and'       => '', // eliminates end to end result
                '-'         => ' ', // catches hyphen and converts it to space
            )
        );

        // convert text to array and numbers(not string as number)
        $parts = array_map(
            function ($val) {
                return (int) $val;
            },
            explode(' ', $data)
        );

        $segmentArray = [];
        $count = 0;

        foreach ($parts as $index => $part) {
            if ($part < 1000) {
                // this segment is for 10s and 1s
                if ($part < 100) {
                    $count += $part;
                }
                // this segment is for hundreds
                else {
                    $count *= $part;
                }
            }
            // if reached a key like (thousand, million, billion...etc) should end the segment
            else {
                $segmentArray[] = $count * $part;
                $count = 0;
            }

            // if last part should be placed on array
            if (count($parts) - 1 === $index) {
                $segmentArray[] = $count;
            }
        }

        return array_sum($segmentArray);
    }

    /**
     * @param int $val
     *
     * @return float
     */
    private function convertUsingAPI(int $val): float
    {

        $response =  HTTP::withHeaders([
            'apikey' => env('CONVERTER_API_KEY')
        ])->get('https://api.apilayer.com/fixer/convert', [
            'from' => 'PHP',
            'to' => 'USD',
            'amount' => $val,
        ])->object();

        return $response->result;
    }
}
