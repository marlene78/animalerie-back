<?php

namespace App\Controller;

use TypeError;
use App\Entity\Type;
use App\Services\EntityLinks;
use Swagger\Annotations as SWG;
use App\Repository\TypeRepository;
use App\Services\SendDataController;
use App\Repository\AnimauxRepository;
use App\Repository\AccessoireRepository;
use App\Repository\NourritureRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/api/type")
 */
class TypeController extends AbstractController
{
  
    
    /**
     * Liste des types
     * @Route("/", name="type_index", methods={"GET"})
     * @SWG\Response(
     *     description="Retourne la liste des types",
     *     response=200,
     *    @Model(type=Type::class , groups={"get:infoType"})
     * )
     * 
     * @param TypeRepository $typeRepository
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    public function index(
        TypeRepository $typeRepository , 
        SendDataController $send ,  
        SerializerInterface $serializer , 
        Request $request
    ):JsonResponse
    {

        try{

            if(count($typeRepository->findAll()) > 0){
            
                return $send->sendData(
                    $serializer->serialize($typeRepository->findAll(),'json',['groups' => 'get:infoType']), 
                    ["POST" => "".$request->server->get('HTTP_HOST')."/type/new"],
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
     * Création d'un nouveau type
     * @Route("/new", name="type_new", methods={"POST"})
     *  @SWG\Response(
     *     description="Création d'un type",
     *     response=201,
     *    @Model(type=Type::class , groups={"get:infoType"})
     * )
     *  @SWG\Parameter(
     *     name="nom",
     *     in="query",
     *     type="string",
     *     description="Nom du type",
     * )
     * @Security(name="Bearer")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function new(
        Request $request , 
        ValidatorInterface $validator ,
        SendDataController $send ,
        SerializerInterface $serializer , 
        EntityLinks $links
    ):JsonResponse
    {
       
        try{

            $type = new Type();
            $type->setNom($request->get('nom')); 
            $errors = $validator->validate($type);
            
            if (count($errors) > 0) {
                $errorTab = []; 

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($type); 
                $em->flush();
         
                return $send->sendData(
                    $serializer->serialize($type,'json',['groups' => 'get:infoType']), 
                    $links->getEntityLinks( $type->getId() ,"POST" , $request->server->get('HTTP_HOST') , "type"),
                    201,
                    "Ressource crée"
                );

            }

        }catch(TypeError $e){
           
            return $send->sendData("", "",400,$e->getMessage());
        }

       
    }



    
    /**
     * Affiche un type en fonction de son ID
     * @Route("/{id}", name="type_show", methods={"GET"} )
    *  @SWG\Response(
     *     description="Retourne un type par son ID",
     *     response=200,
     *    @Model(type=Type::class , groups={"get:infoType"})
     * )
     * @param Type $type
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function show(
        Type $type , 
        SendDataController $send ,
        SerializerInterface $serializer ,
        Request $request ,
        EntityLinks $links
    ):JsonResponse
    {
        return $send->sendData(
            $serializer->serialize($type,'json',['groups' => 'get:infoType']), 
            $links->getEntityLinks( $type->getId() , "GET" , $request->server->get('HTTP_HOST') , "type"),
            200,
            "Ressource trouvée"
        );
          
    }





    
    /**
     * Éditer un type en fonction de son ID
     * @Route("/{id}/edit", name="type_edit", methods={"PUT"})
     *  @SWG\Response(
     *     description="Création d'un type",
     *     response=201,
     *    @Model(type=Type::class , groups={"get:infoType"})
     * )
     *  @SWG\Parameter(
     *     name="nom",
     *     in="query",
     *     type="string",
     *     description="Nom du type",
     * )
     * @Security(name="Bearer")
     * @param Request $request
     * @param TypeRepository $typeRepository
     * @param ValidatorInterface $validator
     * @param SendDataController $send
     * @param SerializerInterface $serializer
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function edit(
        Request $request, 
        TypeRepository $typeRepository,
        ValidatorInterface $validator ,
        SendDataController $send ,
        SerializerInterface $serializer ,
        EntityLinks $links
    ):JsonResponse
    {

        try{
            $type = $typeRepository->find($request->get("id")); 
            $type->setNom($request->get('nom')); 
            $errors = $validator->validate($type);
        
            if (count($errors) > 0) {

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);

            }else{

                $em = $this->getDoctrine()->getManager(); 
                $em->flush(); 
        
                return $send->sendData(
                    $serializer->serialize($type,'json',['groups' => 'get:infoType']), 
                    $links->getEntityLinks( $type->getId() , "PUT" , $request->server->get('HTTP_HOST') , 'type'),
                    201,
                    "Ressource mise à jour"
                );
        
            
            }
        }catch(TypeError $e){

            return $send->sendData("", "",404,$e->getMessage());
        }


       
    }



    
    /**
     * Supprimer un type en fonction de son ID
     * @Route("/{id}", name="type_delete", methods={"DELETE"})
     * @SWG\Response(
     *     description="Supprime un type par son ID",
     *     response=201,
     * )
     * @Security(name="Bearer")
     * @param Type $type
     * @param SendDataController $send
     * @return JsonResponse
     */
    public function delete(Type $type , SendDataController $send , AnimauxRepository $animauxRepository , NourritureRepository $nourritureRepository , AccessoireRepository $accessoireRepository ): JsonResponse
    {

        try{
            $entityManager = $this->getDoctrine()->getManager();
    
            $this->deleteItem( $animauxRepository->findBy(["type" => $type->getId()]) ); 
            $this->deleteItem($nourritureRepository->findBy(["type" => $type->getId()])); 
            $this->deleteItem($accessoireRepository->findBy(["type" => $type->getId()])); 
        

            $entityManager->remove($type);
            $entityManager->flush();

            return $send->sendData("", "",201,"Ressource supprimée");

        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }

       
    }




    private function deleteItem($tab){
        if(count($tab) > 0){
            foreach ($tab as $item) {
                $item->setType(null);
            }
        }
    }




}
