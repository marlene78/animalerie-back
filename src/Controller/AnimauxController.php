<?php

namespace App\Controller;

use TypeError;
use App\Entity\Animaux;
use App\Services\EntityLinks;
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
     * Liste des animaux
     * @Route("/", name="animaux_index", methods={"GET"})
     * @param AnimauxRepository $animauxRepository
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    public function index(
        AnimauxRepository $animauxRepository , 
        SendDataController $send ,  
        SerializerInterface $serializer , 
        Request $request):JsonResponse
    {
        try{

            if(count($animauxRepository->findAll()) > 0){
                return $send->sendData(
                    $serializer->serialize($animauxRepository->findAll(),'json',['groups' => 'get:infoAnimaux']), 
                    ["POST" => "".$request->server->get('HTTP_HOST')."/animaux/new"],
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
     * Liste des 5 derniers animaux
     * @Route("/last", name="animaux_last", methods={"GET"})
     * @param AnimauxRepository $animauxRepository
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    public function getLast(
        AnimauxRepository $animauxRepository , 
        SendDataController $send ,  
        SerializerInterface $serializer , 
        Request $request):JsonResponse
    {
        try{

            if(count($animauxRepository->findLast()) > 0){
                return $send->sendData(
                    $serializer->serialize($animauxRepository->findLast(),'json',['groups' => 'get:infoAnimaux']), 
                    ["POST" => "".$request->server->get('HTTP_HOST')."/animaux/new"],
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
     * Créer un nouveau animal
     * @Route("/new", name="animaux_new", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param TypeRepository $typeRepository
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param EntityLinks $links
     * @param AnimauxRepository $animauxRepository
     * @return JsonResponse
     */
    public function new(
        Request $request ,
        ValidatorInterface $validator , 
        TypeRepository $typeRepository ,
        SendDataController $send ,
        SerializerInterface $serializer , 
        EntityLinks $links,
        AnimauxRepository $animauxRepository
         ):JsonResponse
    {
        
        try{

            $animaux = new Animaux();
            $animaux->setRace($request->get('race')); 
            $animaux->setPoids($request->get('poids')); 
            $animaux->setAge($request->get('age')); 
            $animaux->setPrix($request->get("prix"));
         
            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $animaux->setType($type); 

            $errors = $validator->validate($animaux);
            
            if (count($errors) > 0) {

                
                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($animaux); 
                $em->flush();

                return $send->sendData(
                    $serializer->serialize($animauxRepository->find($animaux->getId()),'json',['groups' => 'get:infoAnimaux']), 
                    $links->getEntityLinks( $animaux->getId() ,"POST" , $request->server->get('HTTP_HOST') , "animaux"),
                    201,
                    "Ressource crée"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,$e->getMessage());
        }

  
    }





 
    /**
     * Affiche un animal en fonction de son ID
     * @Route("/{id}", name="animaux_show", methods={"GET"})
     * @param animaux $animaux
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param EntityLinks $links
     * @param Request $request
     * @return JsonResponse
     */
    public function show(animaux $animaux , SendDataController $send ,  SerializerInterface $serializer , EntityLinks $links , Request $request ):JsonResponse
    { 
        return $send->sendData(
            $serializer->serialize($animaux,'json',['groups' => 'get:infoAnimaux']), 
            $links->getEntityLinks( $animaux->getId() , "GET" , $request->server->get('HTTP_HOST') , "animaux"),
            200,
            "Ressource trouvée"
        );       
    }





  
    /**
     * Éditer un animal en fonction de son ID
     * @Route("/{id}/edit", name="animaux_edit", methods={"PUT"})
     * @param Request $request
     * @param Animaux $animaux
     * @param TypeRepository $typeRepository
     * @param ValidatorInterface $validator
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function edit(Request $request, Animaux $animaux , TypeRepository $typeRepository , ValidatorInterface $validator , SendDataController $send ,  SerializerInterface $serializer , EntityLinks $links ):JsonResponse
    {

        try{
            $animaux->setRace($request->get('race')); 
            $animaux->setPoids($request->get('poids')); 
            $animaux->setAge($request->get('age')); 
            $animaux->setPrix($request->get("prix"));
         
            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
        
            $animaux->setType($type); 
           

            $errors = $validator->validate($animaux);
            
        
            if (count($errors) > 0) {
 
                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{

                $em = $this->getDoctrine()->getManager(); 
                $em->flush(); 
        
                return $send->sendData(
                    $serializer->serialize($animaux,'json',['groups' => 'get:infoAnimaux']), 
                    $links->getEntityLinks( $animaux->getId() , "PUT" , $request->server->get('HTTP_HOST') , 'animaux'),
                    201,
                    "Ressource mise à jour"
                );
        
            
            }
        }catch(TypeError $e){

            return $send->sendData("", "",404,$e->getMessage());
        }




    }







  
    /**
     * Supprimer un animal en fonction de son ID
     * @Route("/{id}", name="animaux_delete", methods={"DELETE"})
     * @param Animaux $animaux
     * @param SendDataController $send
     * @return JsonResponse
     */
    public function delete(Animaux $animaux , SendDataController $send ): JsonResponse
    {
        try{
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($animaux);
            $entityManager->flush();

            return $send->sendData("", "",201,"Ressource supprimée");

        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }

    }









}



