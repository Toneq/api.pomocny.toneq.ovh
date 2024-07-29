<?php

namespace App\Services;

class ResponseService
{
    public function __construct($status, $message, $details, $statusCode = 200)
    {
        $this->response($status, $message, $details, $statusCode);
    }

    private function response($status, $message, $details, $statusCode){
        return response()->json(['status' => $status, 'message' => $message, 'details' => $details], $statusCode);
    }
}
