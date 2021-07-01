<?php 

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;


class SendDataController 
{

       
    public function sendData($data , $links , $statusCode , $message ){

        $dataFormat = [];

        if($data != ""){
            $dataFormat = [
                "data" =>  $data ? json_decode($data) : "" , 
                "links" => $links ? $links : "" , 
                "statusCode" => $statusCode,
                "message" => $message
            ];     
        }else{
            $dataFormat = [
                "statusCode" => $statusCode,
                "message" => $message
            ]; 
        }
      
   
        
        $cache = $statusCode != 200  ? "no-cache" : "public, max-age=1000"; 
    
        return new JsonResponse(
            $dataFormat,
            $statusCode,
            [
                "content-type" => "Application/json",
                "cache-control"  => $cache,
                "HTTP/1.0 ".$statusCode."",
                "Access-Control-Allow-Origin: *",
                "Access-Control-Allow-Headers: *",
                "Access-Control-Allow-Methods: *",
            ],
            false
        );
    }



}