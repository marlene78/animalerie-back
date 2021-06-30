<?php

namespace App\Controller;

use App\Entity\Dons;
use App\Form\DonsType;
use App\Services\EntityLinks;
use App\Repository\DonsRepository;
use App\Services\SendDataController;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/dons")
 */
class DonsController extends AbstractController
{
    /**
     * @Route("/", name="dons_index", methods={"GET"})
     */
    public function index(DonsRepository $donsRepository): Response
    {

        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000"
        ];
        if (count($donsRepository->findAll()) > 0) {
            return $this->json($donsRepository->findAll(), 200 ,  $headers , ['groups' => 'get:infoDons']); 
        }else{
            return $this->json(['status' => 404 , 'message' => 'Liste vide']); 
        }
    }

    /**
     * @Route("/new", name="dons_new", methods={"POST"})
     */
    public function new(
        Request $request,
        DonsRepository $donsRepository, 
        UtilisateurRepository $userRepository, 
        ValidatorInterface $validator , 
        SerializerInterface $serializer,
        EntityLinks $links,
        SendDataController $send 
        ): Response
    {
        $don = new Dons();

        try{
            $don->setMontant($request->get('montant'));
            $don->setMessage($request->get('message')); 

            $user = $userRepository->findOneBy(['id' => $request->get('user') ]); 
            $don->setUser($user); 

            $errors = $validator->validate($don);
            
            if (count($errors) > 0) {
                $errorTab = []; 

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($don); 
                $em->flush();
         
                return $send->sendData(
                    $serializer->serialize($don,'json',['groups' => 'get:infoDons']), 
                    $links->getEntityLinks( $don->getId() ,"POST" , $request->server->get('HTTP_HOST') , "dons"),
                    201,
                    "Ressource crée"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,$e->getMessage());
        }
  
    }

    /**
     * @Route("/{id}", name="dons_show", methods={"GET"})
     */
    public function show(
        Dons $don, 
        Request $request,
        SendDataController $send ,  
        SerializerInterface $serializer,  
        EntityLinks $links): Response
    {
        try{
            return $send->sendData(
                $serializer->serialize($don,'json',['groups' => 'get:infoDons']), 
                $links->getEntityLinks( $don->getId() , "GET" , $request->server->get('HTTP_HOST') , "dons"),
                200,
                "Ressource trouvée"
            );  
            }catch(TypeError $e){
                return $send->sendData("", "",400,$e->getMessage());
            }   
           
    }

    /**
     * @Route("/{id}/edit", name="dons_edit", methods={"PUT"})
     */
    public function edit(
        Request $request,
        DonsRepository $donsRepository, 
        UtilisateurRepository $userRepository, 
        ValidatorInterface $validator , 
        SerializerInterface $serializer,
        EntityLinks $links,
        Dons $don,
        SendDataController $send 
    ): Response
    {
        
        try{
            $don->setMontant($request->get('montant'));
            $don->setMessage($request->get('message')); 

            $user = $userRepository->findOneBy(['id' => $request->get('user') ]); 
            $don->setUser($user); 

            $errors = $validator->validate($don);
            
            if (count($errors) > 0) {
                $errorTab = []; 

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($don); 
                $em->flush();
         
                return $send->sendData(
                    $serializer->serialize($don,'json',['groups' => 'get:infoDons']), 
                    $links->getEntityLinks( $don->getId() ,"PUT" , $request->server->get('HTTP_HOST') , "dons"),
                    201,
                    "Ressource mis à jour"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,$e->getMessage());
        }
    }

    /**
     * @Route("/{id}", name="dons_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Dons $don, SendDataController $send): Response
    {
        try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($don);
            $entityManager->flush();

            return $send->sendData("", "",201,"Ressource supprimée");

        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }
    }
}
