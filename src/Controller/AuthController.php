<?php

namespace App\Controller;

use Exception;
use TypeError;
use Firebase\JWT\JWT;
use App\Services\SendDataController;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;





class AuthController extends AbstractController
{
    /**
     * @Route("/api/login", name="login", methods={"POST"})
     */
    public function login(
        Request $request,
        UtilisateurRepository $utilisateurRepository,
        SendDataController $send
    ): Response {
        try {
         


            if (!$request->get('email') && !$request->get('motDePasse')) {
                return $send->sendData("", "", 400, 'Adresse email ou mot de passe manquant');
            } else {
                $user = $utilisateurRepository->findOneBy(["email" => $request->get('email')]);
                if (!$user) {
                    return $send->sendData("", "", 400, 'Utilisateur non trouvé');
                } 
                 if (!password_verify($request->get('motDePasse'), $user->getMotDePasse())) {
                    return $send->sendData("", "", 400, 'Mot de passe incorrect');

                } 
                    $key = "API";
                    $jwt = JWT::encode($user, $key);


                    return $this->json(['status' => 200, 'token' => $jwt, "data" => $user->getId()]);
                
            }
        } catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }
    }
}
