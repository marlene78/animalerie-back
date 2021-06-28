<?php

namespace App\Controller;

use TypeError;
use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/type")
 */
class TypeController extends AbstractController
{
    /**
     * @Route("/", name="type_index", methods={"GET"})
     */
    public function index(TypeRepository $typeRepository): Response
    {
        $headers = [
            "content-type" => "Application/json",
            "cache-control" => "public, max-age=1000"
        ];

        if(count($typeRepository->findAll()) > 0){
            return $this->json($typeRepository->findAll(), 200 ,  $headers , ['groups' => 'get:infoType']); 
        }else{
            return $this->json(['status' => 404 , 'message' => 'Liste vide']); 
        }
    }

    /**
     * @Route("/new", name="type_new", methods={"GET","POST"})
     */
    public function new(Request $request , ValidatorInterface $validator): Response
    {
        $type = new Type();
        try{
        
            $type->setNom($request->get('nom')); 
   
            $errors = $validator->validate($type);
            

            if (count($errors) > 0) {
                return $this->json($errors , 400);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->flush();
                $em->persist($type); 
                return $this->json($type, 200 , ['groups' => 'get:infoType']); 
            }

        }catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }

       
    }

    /**
     * @Route("/{id}", name="type_show", methods={"GET"})
     */
    public function show(TypeRepository $typeRepository , Request $request): Response
    {
        $type = $typeRepository->find($request->get("id"));
        if($type){ 

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
     * @Route("/{id}/edit", name="type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Type $type): Response
    {
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('type_index');
        }

        return $this->render('type/edit.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_delete", methods={"POST"})
     */
    public function delete(Request $request, Type $type): Response
    {
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($type);
            $entityManager->flush();
        }

        return $this->redirectToRoute('type_index');
    }
}
