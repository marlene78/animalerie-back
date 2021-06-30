<?php

namespace App\Controller;

use TypeError;
use App\Entity\Animaux;
use App\Repository\TypeRepository;
use App\Services\SendDataController;
use App\Repository\AnimauxRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 * @Route("/animaux")
 */
class AnimauxController extends AbstractController
{
    /**
     * @Route("/", name="animaux_index", methods={"GET"})
     */
    public function index(AnimauxRepository $animauxRepository , SendDataController $send ,  SerializerInterface $serializer):JsonResponse
    {
        try{

            if(count($animauxRepository->findAll()) > 0){
                return $send->sendData(
                    $serializer->serialize($animauxRepository->findAll(),'json',['groups' => 'get:infoAnimaux']), 
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
     * @Route("/new", name="animaux_new", methods={"POST"})
     */
    public function new(Request $request ,ValidatorInterface $validator , TypeRepository $typeRepository ,SendDataController $send ,  SerializerInterface $serializer ):JsonResponse
    {
        
        try{

            $animaux = new Animaux();
            $animaux->setRace($request->get('race')); 
            $animaux->setPoids($request->get('poids')); 
            $animaux->setAge($request->get('age')); 
            $animaux->setPrix($request->get("prix"));
         
            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $type != null ? $type->addAnimaux($animaux) : "";
            $animaux->setType($type); 

            $errors = $validator->validate($animaux);
            
            if (count($errors) > 0) {

                return $send->sendData("", "",400,$errors);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($animaux); 
                $em->flush();

                return $send->sendData(
                    $serializer->serialize($animaux,'json',['groups' => 'get:infoAnimaux']), 
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
     * @Route("/{id}", name="animaux_show", methods={"GET"})
     */
    public function show(animaux $animaux , SendDataController $send ,  SerializerInterface $serializer ):JsonResponse
    { 
        return $send->sendData(
            $serializer->serialize($animaux,'json',['groups' => 'get:infoAnimaux']), 
            $this->getEntityLinks(),
            200,
            "Ressource trouvée"
        );       
    }





    /**
     * @Route("/{id}/edit", name="animaux_edit", methods={"PUT"})
     */
    public function edit(Request $request, Animaux $animaux , TypeRepository $typeRepository , ValidatorInterface $validator , SendDataController $send ,  SerializerInterface $serializer):JsonResponse
    {

        try{
            $animaux->setRace($request->get('race')); 
            $animaux->setPoids($request->get('poids')); 
            $animaux->setAge($request->get('age')); 
            $animaux->setPrix($request->get("prix"));
         
            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
        
            if($animaux->getType()!= null && $type != null){
               $animaux->getType()->removeAnimaux($animaux); // old type
            }

            $animaux->setType($type); 
            $type != null ? $type->addAnimaux($animaux) : "";
            

            $errors = $validator->validate($animaux);
            
        
            if (count($errors) > 0) {
 
                return $send->sendData("", "",400,$errors);

            }else{

                $em = $this->getDoctrine()->getManager(); 
                $em->flush(); 
        
                return $send->sendData(
                    $serializer->serialize($animaux,'json',['groups' => 'get:infoAnimaux']), 
                    $this->getEntityLinks(),
                    200,
                    "Ressource mise à jour"
                );
        
            
            }
        }catch(TypeError $e){

            return $send->sendData("", "",404,$e->getMessage());
        }




    }







    /**
     * @Route("/{id}", name="animaux_delete", methods={"DELETE"})
     */
    public function delete(Animaux $animaux , SendDataController $send ): JsonResponse
    {
        try{
    
            $entityManager = $this->getDoctrine()->getManager();

            if($animaux->getType() != null){
                $type = $animaux->getType(); 
                $type->removeAnimaux($animaux);
            }

            $entityManager->remove($animaux);
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
            "GET" => "localhost:5000/animaux",
            "GET" => "localhost:5000/animaux/{id}/",
            "POST" => "localhost:5000/animaux/new",
            "PUT" => "localhost:5000/animaux/{id}/edit",
            "DELETE" => "localhost:5000/animaux/{id}"
        ];
    }



}
