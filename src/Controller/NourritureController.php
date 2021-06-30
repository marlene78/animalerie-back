<?php

namespace App\Controller;

use TypeError;
use App\Entity\Nourriture;
use App\Services\EntityLinks;
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
     * Affiche la liste des nourritures
     * @Route("/", name="nourriture_index", methods={"GET"})
     * @param NourritureRepository $nourritureRepository
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    public function index(
        NourritureRepository $nourritureRepository , 
        SendDataController $send ,  
        SerializerInterface $serializer , 
        Request $request
        ):JsonResponse
    {
        try{

            if(count($nourritureRepository->findAll()) > 0){
                return $send->sendData(
                    $serializer->serialize($nourritureRepository->findAll(),'json',['groups' => 'get:infoFood']), 
                    ["POST" => "".$request->server->get('HTTP_HOST')."/nourriture/new"],
                    200,
                    "Ressources trouvées"
                );
            }else{
                return $send->sendData("", "",404,"Liste vide");
            }

        }catch(TypeError $e){

            return $send->sendData("", "",400,$e->getMessage());
        }
     
    
    }





  
    /**
     * Création d'une nourriture
     * @Route("/new", name="nourriture_new", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param TypeRepository $typeRepository
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function new(
        Request $request ,
        ValidatorInterface $validator , 
        TypeRepository $typeRepository ,
        SendDataController $send ,  
        SerializerInterface $serializer ,  
        EntityLinks $links ,
        NourritureRepository $nourritureRepository
        
        ):JsonResponse
    {
        
        try{

            $nourriture = new Nourriture();
            $nourriture->setNom($request->get('nom')); 
            $nourriture->setDescription($request->get('description')); 
            $nourriture->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
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
                    $serializer->serialize($nourritureRepository->find($nourriture->getId()),'json',['groups' => 'get:infoFood']), 
                    $links->getEntityLinks( $nourriture->getId() ,"POST" , $request->server->get('HTTP_HOST') , "nourriture"),
                    201,
                    "Ressource crée"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,"Veuillez saisir un prix valide.");
        }

       


   
  
    }





  
    /**
     * Affiche une nourriture en fonction de son ID
     * @Route("/{id}", name="nourriture_show", methods={"GET"})
     * @param Nourriture $nourriture
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function show(Nourriture $nourriture , SendDataController $send ,  SerializerInterface $serializer , Request $request ,  EntityLinks $links ,  NourritureRepository $repo  ):JsonResponse
    { 
    
        return $send->sendData(
            $serializer->serialize($nourriture,'json',['groups' => 'get:infoFood']), 
            $links->getEntityLinks( $nourriture->getId() , "GET" , $request->server->get('HTTP_HOST') , "nourriture"),
            200,
            "Ressource trouvée"
        );       
    }





   
    /**
     * Édition d'une nourriture en fonction de son ID
     * @Route("/{id}/edit", name="nourriture_edit", methods={"PUT"})
     * @param Request $request
     * @param Nourriture $nourriture
     * @param TypeRepository $typeRepository
     * @param ValidatorInterface $validator
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function edit(
        Request $request, 
        Nourriture $nourriture , 
        TypeRepository $typeRepository , 
        ValidatorInterface $validator , 
        SendDataController $send ,  
        SerializerInterface $serializer , 
        EntityLinks $links):JsonResponse
    {

        try{
            $nourriture->setNom($request->get('nom')); 
            $nourriture->setDescription($request->get('description')); 
            $nourriture->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
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
                    $links->getEntityLinks( $nourriture->getId() , "PUT" , $request->server->get('HTTP_HOST') , 'nourriture'),
                    201,
                    "Ressource mise à jour"
                );
        
            
            }
        }catch(TypeError $e){

            return $send->sendData("", "",404,$e->getMessage());
        }




    }







   
    /**
     * Supprimer une nourriture en fonction de son ID
     * @Route("/{id}", name="nourriture_delete", methods={"DELETE"})
     * @param Nourriture $nourriture
     * @param SendDataController $send
     * @return JsonResponse
     */
    public function delete(Nourriture $nourriture , SendDataController $send ): JsonResponse
    {
        try{
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nourriture);
            $entityManager->flush();

            return $send->sendData("", "",201,"Ressource supprimée");

        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }

    }









}
