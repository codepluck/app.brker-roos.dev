<?php 

namespace Modules\Abstracts\Traits;

trait ResponseTrait
{
    public function response($status = true, $message = '', $data = [], $errors = [], $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $code);
    }
}