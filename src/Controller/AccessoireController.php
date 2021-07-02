<?php

namespace App\Controller;

use TypeError;
use App\Entity\Accessoire;
use App\Form\AccessoireType;
use App\Services\EntityLinks;
use Swagger\Annotations as SWG;
use App\Repository\TypeRepository;
use App\Services\SendDataController;
use App\Repository\AccessoireRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/accessoire")
 */
class AccessoireController extends AbstractController
{
    /**
     * Liste des accessoires
     * @Route("/", name="accessoire_index", methods={"GET"})
     * @SWG\Response(
     *     description="Retourne la liste des accessoires",
     *     response=200,
     *    @Model(type=Accessoire::class , groups={"get:infoAccessoire"})
     * )
     */
    public function index(Request $request, AccessoireRepository $accessoireRepository, SendDataController $send , SerializerInterface $serializer): Response
    {
        try{

            if(count($accessoireRepository->findAll()) > 0){
            
                return $send->sendData(
                    $serializer->serialize($accessoireRepository->findAll(),'json',['groups' => 'get:infoAccessoire']), 
                    ["POST" => "".$request->server->get('HTTP_HOST')."/accessoire/new"],
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
     * Création d'un accessoire
     * @Route("/new", name="accessoire_new", methods={"POST"})
     *  @SWG\Response(
     *     description="Création d'un accessoire",
     *     response=201,
     *    @Model(type=Accessoire::class , groups={"get:infoAccessoire"})
     * )
     *  @SWG\Parameter(
     *     name="nom",
     *     in="query",
     *     type="string",
     *     description="Nom de l'accessoire",
     * )
     *  @SWG\Parameter(
     *     name="prix",
     *     in="query",
     *     type="number",
     *     description="Prix de l'accessoire"
     * )
     *  @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     type="string",
     *     description="type d'animal"
     * )
     * @Security(name="Bearer")
     */
    public function new(
        Request $request, 
        ValidatorInterface $validator , 
        SerializerInterface $serializer,
        TypeRepository $typeRepository, 
        SendDataController $send, 
        AccessoireRepository $accessoireRepository,
        EntityLinks $links):JsonResponse
    {
        $accessoire = new Accessoire();

        try{
            $accessoire->setNom($request->get('nom'));
            $accessoire->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $accessoire->setType($type); 
           
            $errors = $validator->validate($accessoire);
            
            if (count($errors) > 0) {
                $errorTab = []; 

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($accessoire); 
                $em->flush();
         
                return $send->sendData(
                    $serializer->serialize($accessoireRepository->find($accessoire->getId()),'json',['groups' => 'get:infoAccessoire']), 
                    $links->getEntityLinks( $accessoire->getId() ,"POST" , $request->server->get('HTTP_HOST') , "accessoire"),
                    201,
                    "Ressource crée"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,$e->getMessage());
        }

        
    }

    /**
     * Affiche un accessoire en fonction de son ID
     * @Route("/{id}", name="accessoire_show", methods={"GET"})
     *   @SWG\Response(
     *     description="Retourne un accessoire par son ID",
     *     response=200,
     *    @Model(type=Accessoire::class , groups={"get:infoAccessoire"})
     * )
     */
    public function show(Accessoire $accessoire, SendDataController $send ,  SerializerInterface $serializer , Request $request ,  EntityLinks $links): Response
    {
       try{
        return $send->sendData(
            $serializer->serialize($accessoire,'json',['groups' => 'get:infoAccessoire']), 
            $links->getEntityLinks( $accessoire->getId() , "GET" , $request->server->get('HTTP_HOST') , "accessoire"),
            200,
            "Ressource trouvée"
        );  
        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }   
    }

    /**
     * Édition d'un accessoire
     * @Route("/{id}/edit", name="accessoire_edit", methods={"PUT"})
     *  @SWG\Response(
     *     description="Édition d'un accessoire",
     *     response=201,
     *    @Model(type=Accessoire::class , groups={"get:infoAccessoire"})
     * )
     *  @SWG\Parameter(
     *     name="nom",
     *     in="query",
     *     type="string",
     *     description="Nom de l'accessoire",
     * )
     *  @SWG\Parameter(
     *     name="prix",
     *     in="query",
     *     type="number",
     *     description="Prix de l'accessoire"
     * )
     *  @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     type="string",
     *     description="type d'animal"
     * )
     * @Security(name="Bearer")
     */
    public function edit(Request $request, SendDataController $send,  EntityLinks $links, SerializerInterface $serializer, ValidatorInterface $validator, Accessoire $accessoire, TypeRepository $typeRepository): Response
    {
        try{
            $accessoire->setNom($request->get('nom'));
            $accessoire->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $accessoire->setType($type); 
           
            $errors = $validator->validate($accessoire);
            
        
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
                    $serializer->serialize($accessoire,'json',['groups' => 'get:infoAccessoire']), 
                    $links->getEntityLinks( $accessoire->getId() , "PUT" , $request->server->get('HTTP_HOST') , 'accessoire'),
                    201,
                    "Ressource mise à jour"
                );
        
            
            }
        }catch(TypeError $e){

            return $send->sendData("", "",404,$e->getMessage());
        }
      
    }

    /**
     * Supprime un accessoire
     * @Route("/{id}", name="accessoire_delete", methods={"DELETE"})
     *   @SWG\Response(
     *     description="Supprime un accessoire par son ID",
     *     response=201,
     * )
     * @Security(name="Bearer")
     */
    public function delete(Request $request, Accessoire $accessoire, SendDataController $send): Response
    {

        try{
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($accessoire);
            $entityManager->flush();

            return $send->sendData("", "",201,"Ressource supprimée");

        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }
    }
}
