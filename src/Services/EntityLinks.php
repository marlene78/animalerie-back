<?php 

namespace App\Services;


class EntityLinks{


   
    /**
     * Renvoi la liste des links
     * @return array
     */
    public function getEntityLinks ($id , $method , $uri , $entity){

        switch ($method) {
            case 'GET':
                return[
                    "PUT" => "".$uri."/".$entity."/".$id."/edit",
                    "DELETE" => "".$uri."/".$entity."/".$id."", 
                ];
                break;

            case 'POST':
                return[
                    "GET" => "".$uri."/".$entity."/".$id."/",
                    "PUT" => "".$uri."/".$entity."/".$id."/edit",
                    "DELETE" => "".$uri."/".$entity."/".$id.""
                ];
                break;

            case 'PUT':
                return[
                    "GET" => "".$uri."/".$entity."/".$id."/",
                    "DELETE" => "".$uri."/".$entity."".$id.""
                ];
                break;
            
            default:
                break;
        }

    }




}