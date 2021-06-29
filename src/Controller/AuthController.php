<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use TypeError;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Response;




class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(
        Request $request,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        try {
            $headers = [
                "content_type" => "application/json",
                "cache-control" => "public, max-age=1000"

            ];


            if (!$request->get('email') && !$request->get('motDePasse')) {
                throw new Exception('Adresse email ou mot de passe manquant', 400);
            } else {
                $user = $utilisateurRepository->findOneBy(["email" => $request->get('email')]);
                if (!$user) {
                    throw new Exception('Utilisateur non trouvé', 400);
                } 
                 if (!password_verify($request->get('motDePasse'), $user->getMotDePasse())) {
                    throw new Exception ('Mot de passe incorrect', 400);
                } 
                    $key = "API";
                    $jwt = JWT::encode($user, $key);


                    return $this->json(['status' => 200, 'token' => $jwt]);
                
            }
        } catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }
    }
}
