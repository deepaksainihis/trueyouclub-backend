<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    public function sendResponse($result, $message, $accessToken=''){
    	$response = [
            'status'        => true,
            'result'        => $result,
            'message'       => $message,
            'access_token'  => $accessToken
        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 422){
    	$response = [
            'status'  => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['errors'] = $errorMessages;
        }
        return response()->json($response, $code);

    }

}