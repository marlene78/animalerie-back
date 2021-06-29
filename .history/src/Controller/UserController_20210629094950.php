<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;

class UserController extends AbstractController
{

    /**
     * @Route("/user", name="user_create", methods={"POST"})
     */
    public function create(Request $request,  UtilisateurRepository $utilisateurRepository, )
    {
        $headers = [
            "content_type" => "application/json",
            "cache-control" => "public, max-age=1000",
            
        ];
        $utilisateur = new Utilisateur();
        $utilisateur->setMotDePasse($request->get('motDepasse'));
        $utilisateur->setEmail($request->get('email'));
        $utilisateur->setPseudo($request->get('pseudo'));
        $utilisateur->setAdresse($request->get('adresse'));


        // $payload = json_decode(, true);

        

        die();

    }
}
