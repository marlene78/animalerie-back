<?php 

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;


class SendDataController 
{

       
    public function sendData($data , $links , $statusCode , $message ){
      
        $dataFormat = [
            "data" =>  $data ? json_decode($data) : "" , 
            "links" => $links ? $links : "" , 
            "statusCode" => $statusCode,
            "message" => $message
        ]; 

        return new JsonResponse(
            $dataFormat,
            $statusCode,
            [
                "content-type" => "Application/json",
                "cache-control" => "public, max-age=1000",
            ],
            false
        );
    }




    public function getLinks(){

    }



}