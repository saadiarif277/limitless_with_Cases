<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Helpers\CustomHelper;
use Kunnu\Dropbox\Models\AccessToken;

class DropboxController extends Controller
{
    private $appKey;
    private $appSecret;
    private $redirectUri;
    private $refresh_token;
    private $token;

    public function __construct(){
        $this->appKey = config('services.dropbox.client_id');
        $this->appSecret = config('services.dropbox.client_secret');
        $this->refresh_token = config('services.dropbox.refresh_token');
        $this->accesstoken();
    }

    public function accesstoken()
    {
        $app = new DropboxApp($this->appKey, $this->appSecret);
        $dropbox = new Dropbox($app);

        $accessToken = new AccessToken(['refresh_token'=>$this->refresh_token]);

        $result = $dropbox->getAuthHelper()->getRefreshedAccessToken($accessToken);

        $this->token = $result->getToken();

    }

    public function index(Request $request)
    {
        $code = $request->code;
        echo $code;
        $app = new DropboxApp($this->appKey, $this->appSecret);
        $dropbox = new Dropbox($app);

        $accessToken = $dropbox->getAuthHelper()->getAccessToken($code);

        config(['services.dropbox.refresh_token' => $accessToken->getRefreshToken()]);

        CustomHelper::trackInfo($accessToken);
        // return response()->json($accessToken);
    }

    public function redirectToProvider()
    {
        $app = new DropboxApp($this->appKey, $this->appSecret);
        $dropbox = new Dropbox($app);
        $authUrl = $dropbox->getAuthHelper()->getAuthUrl(null, [], null, 'offline');
        return redirect()->away($authUrl);
    }

    public function moveFolder($fromPath, $toPath){
        // $filePath = "/parent/child";
        $app = new DropboxApp($this->appKey, $this->appSecret, $this->token);
        $dropbox = new Dropbox($app);
        try {
            // Get the list of items in the folder
            $dropbox->listFolder("/$fromPath");
       $dropbox->move("/$fromPath", "/$toPath");
            return true;
        } catch (\Exception $e) {
            // If an exception occurs, the folder doesn't exist or there was an error
            // CustomHelper::trackInfo($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function uploadFile2Dropbox($file, $fileName, $folder){

        $filePath = "/$folder/$fileName";
        $folderPath = "/$folder";

        $app = new DropboxApp($this->appKey, $this->appSecret, $this->token);
        $dropbox = new Dropbox($app);

        // CustomHelper::trackInfo($dropbox);
        $folderExists = false;
        try {
            // Get the list of items in the folder
            $folderItems = $dropbox->listFolder($folderPath);

            // If the folder is present in the list, it exists
            $folderExists = true;
        } catch (\Exception $e) {
            // If an exception occurs, the folder doesn't exist or there was an error
            $folderExists = false;
        }

        if (!$folderExists) {
            try {
                $dropbox->createFolder($folderPath);
            } catch (DropboxClientException $e) {
                return $e->getMessage();
            }
        }

        try {
            $dropboxFile = new DropboxFile($file);
            // Upload the file to Dropbox
            $uploadedFile = $dropbox->upload($dropboxFile, $filePath, ['autorename' => true]);
       return true;
            // return response()->json(['message' => 'File uploaded and shared successfully', 'file' => $uploadedFile]);
        } catch (DropboxClientException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return false;
    }
    public function uploadStorageFile2Dropbox($patient_info, $fileName, $folder){
        $filePath = public_path("uploads/$folder/$fileName");
        $content = file_get_contents($filePath);
        // CustomHelper::trackInfo($content);
        if ($content) {
            $tempFilePath = tempnam(sys_get_temp_dir(), 'temp_file');
            file_put_contents($tempFilePath, $content);
            $result = $this->uploadFile2Dropbox($tempFilePath, $fileName, "unpaid/$patient_info");
            unlink($tempFilePath);
            return $result;
         } else {
            abort(404, 'File not found');
        }
        return false;
    }

    public function test(){

        $this->moveFolder('unpaid/Ankur Bora', 'paid/Ankur Bora');
        // CustomHelper::trackInfo($dropbox);
    }
}
