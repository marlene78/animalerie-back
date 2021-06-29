<?php

namespace App\Controller;

use TypeError;
use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/type")
 */
class TypeController extends AbstractController
{
    /**
     * @Route("/", name="type_index", methods={"GET"})
     */
    public function index(TypeRepository $typeRepository): Response
    {
        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000",
            "access-control-method" => "GET"
        ];

        if(count($typeRepository->findAll()) > 0){
            return $this->json($typeRepository->findAll(), 200 ,  $headers , ['groups' => 'get:infoType']); 
        }else{
            return $this->json(['status' => 404 , 'message' => 'Liste vide']); 
        }
    }




    /**
     * @Route("/new", name="type_new", methods={"POST"})
     */
    public function new(Request $request , ValidatorInterface $validator): Response
    {
        $type = new Type();
        try{
        
            $type->setNom($request->get('nom')); 
   
            $errors = $validator->validate($type);
            

            if (count($errors) > 0) {
                return $this->json($errors , 400);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($type); 
                $em->flush();
                return $this->json($type, 200 , ['groups' => 'get:infoType']); 
            }

        }catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }

       
    }



    /**
     * @Route("/{id}", name="type_show", methods={"GET"})
     */
    public function show(TypeRepository $typeRepository , Request $request): Response
    {
        $type = $typeRepository->find($request->get("id"));
        if($type){ 

            $headers = [
                "content-type" => "Application/json",
                "cache-control" => "public, max-age=1000", 
                "access-control-method" => "GET"
            ];
            
            return $this->json($type, 200 ,  $headers , ['groups' => 'get:infoType']); 

        }else{
            return $this->json(['status' => 404 , 'message' => "Ressource non trouvé"]); 
        }
             
           
    }





    /**
     * @Route("/{id}/edit", name="type_edit", methods={"PUT"})
     */
    public function edit(Request $request, Type $type , ValidatorInterface $validator ): Response
    {
        try{
            $type->setNom($request->get('nom')); 
            $errors = $validator->validate($type);
            

            if (count($errors) > 0) {
                return $this->json($errors , 400);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($type); 
                return $this->json($type, 201 , ['groups' => 'get:infoType']); 
            }

        }catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }


       
    }

    /**
     * @Route("/{id}", name="type_delete", methods={"DELETE"})
     */
    public function delete(Type $type): Response
    {
       
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($type);
        $entityManager->flush();

        return $this->json([
            "status" => 201,
            "message" => "Ressource supprimé"
        ]); 
    }
}
