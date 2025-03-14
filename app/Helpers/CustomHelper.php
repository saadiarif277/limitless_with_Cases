<?php
namespace App\Helpers;

use App\Http\Controllers\Controller;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Dropbox;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomHelper extends Controller{
    //Generate Random String
    public function redirect(Request $request)
    {
        $code = $request['code'];
        Session::put('dropbox_auth_code', $code);

        return response()->json([
            'message' => 'Received Dropbox auth Code successfully. Please go back to upload and try now.',
            'code' => $code,
        ]);
    }
    public function getName(){
        return 'Custom Helper Class';
    }
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    //Print Date Format by Y-m-d
    public static function dateFormat($date,$format)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($format);
    }

    public static function trackInfo($val, $exit=true){
        if($val){
            echo '**********<br>';
            echo '<pre>';
            print_r($val);
            echo '</pre>';
            echo '**********<br>';
        }else{
            echo 'value is empty';
        }
        if($exit) exit;
    }
}
