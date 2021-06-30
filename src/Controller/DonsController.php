<?php

namespace App\Controller;

use App\Entity\Dons;
use App\Form\DonsType;
use App\Repository\DonsRepository;
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
    public function new(Request $request,DonsRepository $donsRepository, UtilisateurRepository $userRepository, ValidatorInterface $validator , SerializerInterface $serializer): Response
    {
        $don = new Dons();
        
        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000"
        ];

        try {
            $don->setMontant($request->get('montant'));
            $don->setMessage($request->get('message')); 

            $user = $userRepository->findOneBy(['id' => $request->get('user') ]); 
            $don->setUser($user); 

            $errors = $validator->validate($don);
            if (count($errors) > 0) {
                return $this->json($errors , 400);
            }else {
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($don); 
                $em->flush();

                $json = $serializer->serialize($donsRepository->find($don->getId()), 'json', ['groups' => ['get:infoDons']]);
                return $this->json(json_decode($json), 201, $headers); 
            }
            
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 400, $headers);
        }
  
    }

    /**
     * @Route("/{id}", name="dons_show", methods={"GET"})
     */
    public function show(Dons $don, Request $request, DonsRepository $donsRepository): Response
    {
        $don = $donsRepository->find($request->get("id"));
        if($don){ 

            $headers = [
                "content-type" => "Application/json",
                "cache-control" => "public, max-age=1000"
            ];
            
            return $this->json($don, 200 ,  $headers , ['groups' => 'get:infoDons']); 

        }else{
            return $this->json(['status' => 404 , 'message' => "Ressource non trouvé"]); 
        }
           
    }

    /**
     * @Route("/{id}/edit", name="dons_edit", methods={"PUT"})
     */
    public function edit(Request $request, Dons $don, DonsRepository $donsRepository, UtilisateurRepository $userRepository, ValidatorInterface $validator , SerializerInterface $serializer): Response
    {
        
        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000"
        ];

        try {
            $don->setMontant($request->get('montant'));
            $don->setMessage($request->get('message')); 

            $user = $userRepository->findOneBy(['id' => $request->get('user') ]); 
            $don->setUser($user); 

            $errors = $validator->validate($don);
            if (count($errors) > 0) {
                return $this->json($errors , 400);
            }else {
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($don); 
                $em->flush();

                $json = $serializer->serialize($donsRepository->find($don->getId()), 'json', ['groups' => ['get:infoDons']]);
                return $this->json(json_decode($json), 201, $headers); 
            }
            
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 400, $headers);
        }
    }

    /**
     * @Route("/{id}", name="dons_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Dons $don): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($don);
        $entityManager->flush();

        return $this->json([
            "status" => 201,
            "message" => "Ressource supprimé"
        ]);
    }
}
