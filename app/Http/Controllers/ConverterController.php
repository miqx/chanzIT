<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConverterController extends Controller
{
    public function index(Request $request)
    {
        if(is_numeric( $request->answer)){
           $data['conversion'] = $this->convertToWords($request->answer);
        }
        else {
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
    private function convertToWords(string $val) {
        // some code here
    }

    /**
     * @param string $val - string that is in aphabet format
     *
     * @return int
     */
    private function convertToNumbers(string $val) {

        // convert corresponding text to symbols (copied from internet didnt want to type the whole array too time consuming).
        $data = strtr(
            $val,
            array(
                'zero'      => '0',
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

        foreach($parts as $index => $part) {
            if($part < 1000)
            {
                // this segment is for 10s and 1s
                if($part < 100) {
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
            if(count($parts) - 1 === $index) {
                $segmentArray[] = $count;
            }
        }

        return array_sum($segmentArray);
    }

    /**
     * @param int $val
     *
     * @return int
     */
    private function convertUsingAPI(int $val) {
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

