<?php

namespace App\Controller;

use TypeError;
use App\Entity\Dons;
use App\Services\EntityLinks;
use Swagger\Annotations as SWG;
use App\Repository\DonsRepository;
use App\Services\SendDataController;
use App\Repository\UtilisateurRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Security;



/**
 * @Route("/api/dons")
 */
class DonsController extends AbstractController
{
   
    /**
     * Création d'un dons
     * @Route("/new", name="dons_new", methods={"POST"})
     *  @SWG\Response(
     *     description="Création d'un dons",
     *     response=201,
     *      @Model(type=Dons::class , groups={"get:infoDons"})
     * )
     *  @SWG\Parameter(
     *     name="montant",
     *     in="query",
     *     type="number",
     *     description="Montant du don",
     * )
     *  @SWG\Parameter(
     *     name="message",
     *     in="query",
     *     type="string",
     *     description="Message"
     * )
     *  @SWG\Parameter(
     *     name="user",
     *     in="query",
     *     type="integer",
     *     description="Identifiant de l'utilisateur"
     * )
     * @Security(name="Bearer")
     */
    public function new(
        Request $request, 
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
     * Affiche un don en fonction de son ID
     * @Route("/{id}", name="dons_show", methods={"GET"})
     *  @SWG\Response(
     *     description="Retourne un dons par son ID",
     *     response=200,
     *    @Model(type=Dons::class , groups={"get:infoDons"})
     * )
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







}
