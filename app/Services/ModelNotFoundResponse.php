<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * trait to use with $this
 */
trait ModelNotFoundResponse
{
    /**
     * if the model is null, return 404 response
     * @param \Illuminate\Database\Eloquent\Model|null $model
     */
    protected function errorModelJsonResponse($model, $data = ['error' => 'Resource not found.'], $errorCode = 404)
    {
        if (!$model) {
            throw new HttpResponseException(response()->json($data, $errorCode));
        }
    }
}
