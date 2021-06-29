<?php

namespace App\Controller;

use TypeError;
use App\Entity\Type;
use PhpParser\Node\Stmt\TryCatch;
use App\Repository\TypeRepository;
use App\Services\SendDataController;
use Doctrine\DBAL\Types\TypeRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/type")
 */
class TypeController extends AbstractController
{
    /**
     * @Route("/", name="type_index", methods={"GET"})
     */
    public function index(TypeRepository $typeRepository , SendDataController $send ,  SerializerInterface $serializer)
    {

       
        try{

            if(count($typeRepository->findAll()) > 0){
            
                return $send->sendData(
                    $serializer->serialize($typeRepository->findAll(),'json',['groups' => 'get:infoType']), 
                    $this->getEntityLinks(),
                    200,
                    "Ressources trouvées"
                );


            }else{

                return $send->sendData("", $this->getEntityLinks(),404,"Liste vide");
            }

        }catch(TypeError $e){

            return $send->sendData("", "",400,$e->getMessage());
        }
    }




    /**
     * @Route("/new", name="type_new", methods={"POST"})
     */
    public function new(Request $request , ValidatorInterface $validator ,SendDataController $send ,  SerializerInterface $serializer)
    {
       
        try{

            $type = new Type();
            $type->setNom($request->get('nom')); 
            $errors = $validator->validate($type);
            
            if (count($errors) > 0) {
                $errors == "" ?  $errors = "".$request->get('nom')." existe déjà" : $errors; 
                return $send->sendData("", "",400,$errors);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($type); 
                $em->flush();
         
                return $send->sendData(
                    $serializer->serialize($type,'json',['groups' => 'get:infoType']), 
                    $this->getEntityLinks(),
                    200,
                    "Ressource mise à jour"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,$e->getMessage());
        }

       
    }



    /**
     * @Route("/{id}", name="type_show", methods={"GET"} )
     */
    public function show(Type $type , SendDataController $send ,  SerializerInterface $serializer): JsonResponse
    {
        return $send->sendData(
            $serializer->serialize($type,'json',['groups' => 'get:infoType']), 
            $this->getEntityLinks(),
            200,
            "Ressource trouvée"
        );
          
    }





    /**
     * @Route("/{id}/edit", name="type_edit", methods={"PUT"})
     */
    public function edit(Request $request, TypeRepository $typeRepository, ValidatorInterface $validator ,  SendDataController $send ,  SerializerInterface $serializer): JsonResponse
    {

        try{
            $type = $typeRepository->find($request->get("id")); 
            $type->setNom($request->get('nom')); 
            $errors = $validator->validate($type);
        
            if (count($errors) > 0) {

                $errors == "" ?  $errors = "".$request->get('nom')." existe déjà" : $errors; 
                return $send->sendData("", "",400,$errors);

            }else{

                $em = $this->getDoctrine()->getManager(); 
                $em->flush(); 
        
                return $send->sendData(
                    $serializer->serialize($type,'json',['groups' => 'get:infoType']), 
                    $this->getEntityLinks(),
                    200,
                    "Ressource mit à jour"
                );
        
            
            }
        }catch(TypeError $e){

            return $send->sendData("", "",404,$e->getMessage());
        }


       
    }



    
    /**
     * @Route("/{id}", name="type_delete", methods={"DELETE"})
     */
    public function delete(Type $type , SendDataController $send ):JsonResponse
    {
        try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($type);
            $entityManager->flush();

            return $send->sendData("", $this->getEntityLinks(),201,"Ressource supprimée");

        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }

       
    }





    /**
     * Renvoi la liste des links
     * @return array
     */
    public function getEntityLinks (){
        return[
            "GET" => "localhost:5000/type",
            "GET" => "localhost:5000/type/{id}/",
            "POST" => "localhost:5000/type/new",
            "PUT" => "localhost:5000/type/{id}/edit",
            "DELETE" => "localhost:5000/type/{id}"
        ];
    }



}
