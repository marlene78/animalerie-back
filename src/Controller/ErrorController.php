<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;



class ErrorController {

    public function show(Exception $exception){
        return new JsonResponse([
            'message' => $exception->getMessage(),
        ]);
    }

}