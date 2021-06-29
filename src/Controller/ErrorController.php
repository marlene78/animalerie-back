<?php

namespace App\Controller;


use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;



class ErrorController {

    public function show(FlattenException $exception){ 
      
        return new JsonResponse([
            'message' => $exception->getStatusText(),
            'statusCode' => $exception->getStatusCode()
        ]);
    }

}