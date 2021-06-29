<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user", name="user_create", methods="{POST}")
     */
    public function create(Request $request,  UtilisateurRepository $utilisateurRepository)
    {
        $headers = [
            "content_type" => "application/json",
            "cache-control" => "public, max-age=1000"
        ];

        // $payload = json_decode(, true);

        var_dump($request->getContent());

    }
}
