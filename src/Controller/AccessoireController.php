<?php

namespace App\Controller;

use App\Entity\Accessoire;
use App\Form\AccessoireType;
use App\Repository\TypeRepository;
use App\Repository\AccessoireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/accessoire")
 */
class AccessoireController extends AbstractController
{
    /**
     * @Route("/", name="accessoire_index", methods={"GET"})
     */
    public function index(AccessoireRepository $accessoireRepository): Response
    {
        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000"
        ];
        if (count($accessoireRepository->findAll()) > 0) {
            return $this->json($accessoireRepository->findAll(), 200 ,  $headers , ['groups' => 'get:infoAccessoire']); 
        }else{
            return $this->json(['status' => 404 , 'message' => 'Liste vide']); 
        }

    }

    /**
     * @Route("/new", name="accessoire_new", methods={"POST"})
     */
    public function new(Request $request,AccessoireRepository $accessoireRepository, ValidatorInterface $validator , SerializerInterface $serializer,TypeRepository $typeRepository): Response
    {
        $accessoire = new Accessoire();

        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000"
        ];

        try {
            $accessoire->setNom($request->get('nom'));
            $accessoire->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $accessoire->setType($type); 

            $errors = $validator->validate($accessoire);
            if (count($errors) > 0) {
                return $this->json($errors , 400);
            }else {
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($accessoire); 
                $em->flush();

                $json = $serializer->serialize($accessoireRepository->find($accessoire->getId()), 'json', ['groups' => ['get:infoAccessoire']]);
                return $this->json(json_decode($json), 201, $headers); 
            }
            
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 400, $headers);
        }

        
    }

    /**
     * @Route("/{id}", name="accessoire_show", methods={"GET"})
     */
    public function show(Accessoire $accessoire,Request $request,  AccessoireRepository $accessoireRepository): Response
    {
        $accessoire = $accessoireRepository->find($request->get("id"));
        if($accessoire){ 

            $headers = [
                "content-type" => "Application/json",
                "cache-control" => "public, max-age=1000"
            ];
            
            return $this->json($accessoire, 200 ,  $headers , ['groups' => 'get:infoAccessoire']); 

        }else{
            return $this->json(['status' => 404 , 'message' => "Ressource non trouvé"]); 
        }
             
         
        
    }

    /**
     * @Route("/{id}/edit", name="accessoire_edit", methods={"PUT"})
     */
    public function edit(Request $request, AccessoireRepository $accessoireRepository, SerializerInterface $serializer, ValidatorInterface $validator, Accessoire $accessoire, TypeRepository $typeRepository): Response
    {
        try{
            $headers = [
                "content-type" => "Application/json",
                "cache-control" => "public, max-age=1000"
            ];
    
            $accessoire->setNom($request->get('nom'));
            $accessoire->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $accessoire->setType($type); 

            $errors = $validator->validate($accessoire);
            

            if (count($errors) > 0) {
        
                return $this->json($errors , 400);
            

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($accessoire); 
                $json = $serializer->serialize($accessoireRepository->find($accessoire->getId()), 'json', ['groups' => ['get:infoAccessoire']]);
                return $this->json(json_decode($json), 201, $headers); 
            }

        }catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }
      
    }

    /**
     * @Route("/{id}", name="accessoire_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Accessoire $accessoire): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($accessoire);
        $entityManager->flush();

        return $this->json([
            "status" => 201,
            "message" => "accessoire supprimé"
        ]); 
    }
}
