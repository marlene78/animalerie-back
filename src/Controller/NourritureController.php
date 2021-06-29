<?php

namespace App\Controller;

use TypeError;
use App\Entity\Nourriture;
use App\Repository\TypeRepository;
use App\Services\SendDataController;
use App\Repository\NourritureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 * @Route("/nourriture")
 */
class NourritureController extends AbstractController
{
    /**
     * @Route("/", name="nourriture_index", methods={"GET"})
     */
    public function index(NourritureRepository $nourritureRepository , SendDataController $send ,  SerializerInterface $serializer):JsonResponse
    {
        try{

            if(count($nourritureRepository->findAll()) > 0){
                return $send->sendData(
                    $serializer->serialize($nourritureRepository->findAll(),'json',['groups' => 'get:infoFood']), 
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
     * @Route("/new", name="nourriture_new", methods={"POST"})
     */
    public function new(Request $request ,ValidatorInterface $validator , TypeRepository $typeRepository ,SendDataController $send ,  SerializerInterface $serializer ):JsonResponse
    {
        
        try{

            $nourriture = new Nourriture();
            $nourriture->setNom($request->get('nom')); 
            $nourriture->setDescription($request->get('description')); 
            $nourriture->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $type->addNourriture($nourriture);
            $nourriture->setType($type); 


            $errors = $validator->validate($nourriture);

            if (count($errors) > 0) {
              
                $errorTab = []; 

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($nourriture); 
                $em->flush();

                return $send->sendData(
                    $serializer->serialize($nourriture,'json',['groups' => 'get:infoFood']), 
                    $this->getEntityLinks(),
                    200,
                    "Ressource mise à jour"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,"Veuillez saisir un prix valide.");
        }

       


   
  
    }





    /**
     * @Route("/{id}", name="nourriture_show", methods={"GET"})
     */
    public function show(Nourriture $nourriture , SendDataController $send ,  SerializerInterface $serializer ):JsonResponse
    { 
        return $send->sendData(
            $serializer->serialize($nourriture,'json',['groups' => 'get:infoFood']), 
            $this->getEntityLinks(),
            200,
            "Ressource trouvée"
        );       
    }





    /**
     * @Route("/{id}/edit", name="nourriture_edit", methods={"PUT"})
     */
    public function edit(Request $request, Nourriture $nourriture , TypeRepository $typeRepository , ValidatorInterface $validator , SendDataController $send ,  SerializerInterface $serializer):JsonResponse
    {

        try{
            $nourriture->setNom($request->get('nom')); 
            $nourriture->setDescription($request->get('description')); 
            $nourriture->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 

            $type->addNourriture($nourriture);
            $nourriture->setType($type); 
    
            $errors = $validator->validate($nourriture);
            
        
            if (count($errors) > 0) {
                $errorTab = []; 

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{

                $em = $this->getDoctrine()->getManager(); 
                $em->flush(); 
        
                return $send->sendData(
                    $serializer->serialize($nourriture,'json',['groups' => 'get:infoFood']), 
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
     * @Route("/{id}", name="nourriture_delete", methods={"DELETE"})
     */
    public function delete(Nourriture $nourriture , SendDataController $send ): JsonResponse
    {
        try{
    
            $entityManager = $this->getDoctrine()->getManager();
            if($nourriture->getType() != null){
                $type = $nourriture->getType(); 
                $type->removeNourriture($nourriture);
            }

            $entityManager->remove($nourriture);
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
            "GET" => "localhost:5000/nourriture",
            "GET" => "localhost:5000/nourriture/{id}/",
            "POST" => "localhost:5000/nourriture/new",
            "PUT" => "localhost:5000/nourriture/{id}/edit",
            "DELETE" => "localhost:5000/nourriture/{id}",
        ];
    }



}
