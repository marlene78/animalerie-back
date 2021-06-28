<?php

namespace App\Controller;

use App\Entity\Nourriture;
use App\Repository\NourritureRepository;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TypeError;



/**
 * @Route("/nourriture")
 */
class NourritureController extends AbstractController
{
    /**
     * @Route("/", name="nourriture_index", methods={"GET"})
     */
    public function index(NourritureRepository $nourritureRepository)
    {
        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000"
        ];

        if(count($nourritureRepository->findAll()) > 0){
            return $this->json($nourritureRepository->findAll(), 200 ,  $headers , ['groups' => 'get:infoFood']); 
        }else{
            return $this->json(['status' => 404 , 'message' => 'Liste vide']); 
        }
     
    
    }

    /**
     * @Route("/new", name="nourriture_new", methods={"POST"})
     */
    public function new(Request $request ,ValidatorInterface $validator , TypeRepository $typeRepository)
    {
        
        $nourriture = new Nourriture();
 
        try{
        
            $nourriture->setNom($request->get('nom')); 
            $nourriture->setDescription($request->get('description')); 
            $nourriture->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $nourriture->setType($type); 

            $errors = $validator->validate($nourriture);
            

            if (count($errors) > 0) {
                return $this->json($errors , 400);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->flush();
                $em->persist($nourriture); 
                return $this->json($nourriture, 200 , ['groups' => 'get:infoFood']); 
            }

        }catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }

       


   
  
    }

    /**
     * @Route("/{id}", name="nourriture_show", methods={"GET"})
     */
    public function show(NourritureRepository $nourritureRepository , Request $request): Response
    { 
        $nourriture = $nourritureRepository->find($request->get("id"));
        if($nourriture){ 

            $headers = [
                "content-type" => "Application/json",
                "cache-control" => "public, max-age=1000"
            ];
            
            return $this->json($nourriture, 200 ,  $headers , ['groups' => 'get:infoFood']); 

        }else{
            return $this->json(['status' => 404 , 'message' => "Ressource non trouvé"]); 
        }
             
           
     
    }

    /**
     * @Route("/{id}/edit", name="nourriture_edit", methods={"PUT"})
     */
    public function edit(Request $request, Nourriture $nourriture , TypeRepository $typeRepository , ValidatorInterface $validator)
    {
        try{
            $nourriture->setNom($request->get('nom')); 
            $nourriture->setDescription($request->get('description')); 
            $nourriture->setPrix($request->get('prix')); 

            $type = $typeRepository->findOneBy(['nom' => $request->get('type') ]); 
            $nourriture->setType($type); 

            $errors = $validator->validate($nourriture);
            

            if (count($errors) > 0) {
        
                return $this->json($errors , 400);
            

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($nourriture); 
                return $this->json($nourriture, 201 , ['groups' => 'get:infoFood']); 
            }

        }catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }



    }



    /**
     * @Route("/{id}", name="nourriture_delete", methods={"DELETE"})
     */
    public function delete(Nourriture $nourriture)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($nourriture);
        $entityManager->flush();

        return $this->json([
            "status" => 201,
            "message" => "Ressource supprimé"
        ]); 
    }
}
