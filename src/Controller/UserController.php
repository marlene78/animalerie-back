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
    public function create(
        Request $request,
        RoleRepository $roleRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UtilisateurRepository $utilisateurRepository
    ) {

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
                return $this->json($errors, 400);
            } else {

                $em = $this->getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->flush();
                $json = $serializer->serialize($utilisateurRepository->find($utilisateur->getId()), 'json', ['groups' => ['get:infoUtilisateur']]);

                return $this->json(json_decode($json), 201, $headers);
            }
        } catch (\Throwable $th) {
            return $this->json($th->getMessage(), 400, $headers);
        }
    }

    /**
     * @Route("/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Utilisateur $utilisateur): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($utilisateur);
        $entityManager->flush();

        return $this->json([
            "status" => 201,
            "message" => "Utilisateur supprimé"
        ]);
    }


    /**
     * @Route("user/{id}", name="user_show", methods={"GET"})
     */
    public function show(UtilisateurRepository $utilisateurRepository, Request $request): Response
    {
        $user = $utilisateurRepository->find($request->get("id"));
        if ($user) {

            $headers = [
                "content-type" => "Application/json",
                "cache-control" => "public, max-age=1000",
                "access-control-method" => "GET"
            ];

            return $this->json($user, 200,  $headers, ['groups' => 'get:infoUtilisateur']);
        } else {
            return $this->json(['status' => 404, 'message' => "Utilisateur non trouvé"]);
        }
    }
}
