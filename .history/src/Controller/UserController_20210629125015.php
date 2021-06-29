<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Repository\RoleRepository;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;


class UserController extends AbstractController
{

    /**
     * @Route("/user", name="user_create", methods={"POST"})
     */
    public function create(Request $request, RoleRepository $roleRepository, 
     ValidatorInterface $validator,  SerializerInterface $serializer )
    {
        
        try {
            $headers = [
                "content_type" => "application/json",
                "cache-control" => "public, max-age=1000"
                
            ];
            $utilisateur = new Utilisateur();
            $utilisateur->setMotDePasse($request->get('motDePasse'));
            $utilisateur->setEmail($request->get('email'));
            $utilisateur->setPseudo($request->get('pseudo'));
            $utilisateur->setAdresse($request->get('adresse'));
            $utilisateur->addRole($roleRepository->findOneBy(["nom" => $request->get('role')]));

            $errors = $validator->validate($utilisateur);
            

            if (count($errors) > 0) {
                return $this->json($errors , 400);

            }else{
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($utilisateur); 
                $em->flush();
                $json = $serializer->serialize($utilisateur, 'json', ['groups' => ['get:infoUtilisateur']]);
                
                return new Response($json, 201, $headers);
            
            }
          

    
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(),400, $headers );
        }


    }
}
