<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{

    public function response($status, $message, $details, $statusCode){
        return response()->json(['status' => $status, 'message' => $message, 'details' => $details], $statusCode);
    }
}
