<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BaseApiController extends ResourceController
{

    protected function success($data = [], $message = 'Success')
    {
        return $this->respond([
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function error($message = 'Error', $code = 400)
    {
        return $this->fail([
            'status' => false,
            'message' => $message
        ], $code);
    }

}