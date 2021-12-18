<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiBaseController extends Controller
{
        
    /**
     * sendResponse
     *
     * @param  mixed $result
     * @param  mixed $message
     * @param  mixed $status
     * @return void
     */
    public function sendResponse($result,$message,$status=404, $code=404)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
            'status'  => $status
        ];
        return response()->json($response,$code);
    }
    
    /**
     * sendSingleFieldError
     *
     * @param  mixed $error
     * @param  mixed $code
     * @param  mixed $errorMessages
     * @return void
     */
    public function sendSingleFieldError($error,$status,$code)
    {
        $response = [
            'success' => false,
            'error'   => $error,
            'status'  => $status
        ];
        return response()->json($response,$code);
    }
}
