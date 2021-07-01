<?php

namespace App\Controller;

use TypeError;
use App\Entity\Utilisateur;
use App\Services\EntityLinks;
use App\Repository\RoleRepository;
use App\Services\SendDataController;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UserController extends AbstractController
{

    /**
     * Création d'un utilisateur
     * @Route("/user/new", name="user_create", methods={"POST"})
     * @param Request $request
     * @param RoleRepository $roleRepository
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param SendDataController $send
     * @param UtilisateurRepository $utilisateurRepository
     * @return JsonResponse
     */
    public function create(
        Request $request,
        RoleRepository $roleRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        SendDataController $send,
        EntityLinks $links,
        UtilisateurRepository $utilisateurRepository
    ): JsonResponse {

        try {
            if (!$request->get("role") || !$request->get("motDePasse") || !$request->get("email") || !$request->get('pseudo')) {
                return $send->sendData("", "", 400, "Paramètre manquant");
            }
            $utilisateur = new Utilisateur();
            $utilisateur->setMotDePasse($request->get('motDePasse'));
            $utilisateur->setEmail($request->get('email'));
            $utilisateur->setPseudo($request->get('pseudo'));
            if ($request->get('adresse')) {
                $utilisateur->setAdresse($request->get('adresse'));
            }
            foreach ($request->get('role') as $role) {
                $utilisateur->addRole($roleRepository->findOneBy(["nom" => $role]));
            }

            $errors = $validator->validate($utilisateur);


            if (count($errors) > 0) {
                $errorTab = [];

                foreach ($errors as $error) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "", 400, $errorTab);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($utilisateur);
            $em->flush();

            return $send->sendData(
                $serializer->serialize($utilisateurRepository->find($utilisateur->getId()), 'json', ['groups' => 'get:infoUtilisateur']),
                $links->getEntityLinks($utilisateur->getId(), "POST", $request->server->get('HTTP_HOST'), "user"),
                201,
                "Utilisateur ajouté"
            );
        } catch (TypeError $e) {
            return $send->sendData("", "", 400, $e->getMessage());
        }
    }

    /**
     * Supprimer un utilisateur en fonction de son ID
     * @Route("/user/{id}", name="user_delete", methods={"DELETE"})
     * @param Utilisateur $utilisateur,
     * @param SendDataController $send
     * @return JsonResponse 
     */
    public function delete(
        Utilisateur $utilisateur,
        SendDataController $send
    ): JsonResponse {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            return $send->sendData("", "", 201, "Utilisateur supprimée");
        } catch (TypeError $e) {
            return $send->sendData("", "", 400, $e->getMessage());
        }
    }


    /**
     * Affichage d'un utilisateur suivant son id 
     * @Route("user/{id}", name="user_show", methods={"GET"})
     * @param Utilisateur $utilisateur 
     * @param Request $request
     * @param SerializerInterface $serializer,      
     * @param EntityLinks $links
     * @param SendDataController $send
     * @return  JsonResponse
     */
    public function show(
        Request $request,
        SerializerInterface $serializer,
        EntityLinks $links,
        SendDataController $send,
        Utilisateur $utilisateur
    ): JsonResponse {
        try {
            return $send->sendData(
                $serializer->serialize($utilisateur, 'json', ['groups' => 'get:infoUtilisateur']),
                $links->getEntityLinks($utilisateur->getId(), "GET", $request->server->get('HTTP_HOST'), "utilisateur"),
                ($utilisateur) ? 200 : 404,
                ($utilisateur) ? "Utilisateur trouvée" : "Utilisateur non trouvée"
            );
        } catch (TypeError $e) {
            return $this->json($e->getMessage(), 400);
        }
    }

    /**
     * Afficher la liste des utilisateurs
     * @Route("/user", name="user_index", methods={"GET"})
     * @param UtilisateurRepository $utilisateurRepository
     * @param SendDataController $send,  
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    public function index(
        UtilisateurRepository $utilisateurRepository,
        SendDataController $send,
        SerializerInterface $serializer,
        Request $request
    ): JsonResponse {

        try {
            $articles = $utilisateurRepository->findAll();
            return $send->sendData(
                $serializer->serialize($utilisateurRepository->findAll(), 'json', ['groups' => 'get:infoUtilisateur']),
                ["POST" => "" . $request->server->get('HTTP_HOST') . "/user/new"],
                200,
                (count($articles) > 0) ? 'Utilisateur trouvés' : 'Aucun utilisateur'
            );
        } catch (TypeError $e) {
            return $this->json($e->getMessage(), 400);
        }
    }


    /**
     * Edition d'un utilisateur en fonction de son ID
     * @Route("user/{id}/edit", name="user_edit", methods={"PUT"})
     * @param Request $request, 
     * @param Utilisateur $utilisateur, 
     * @param ValidatorInterface $validator, 
     * @param SendDataController $send,  
     * @param SerializerInterface $serializer, 
     * @param EntityLinks $links
     * @param RoleRepository $roleRepository
     * @return JsonResponse
     */
    public function edit(
        Request $request,
        Utilisateur $utilisateur,
        ValidatorInterface $validator,
        SendDataController $send,
        SerializerInterface $serializer,
        EntityLinks $links,
        RoleRepository $roleRepository
    ): JsonResponse {

        try {



            if (!$request->get('pseudo')  && !$request->get('motDePasse') && !$request->get('email') && !$request->get('adresse') && !$request->get('role')) {
                return $send->sendData("", "", 400, "Paramètre manquant");
            }
            if ($request->get('pseudo') || $request->get('email')) {
                if ($request->get('pseudo')) {
                    $utilisateur->setPseudo($request->get('pseudo'));
                }

                if ($request->get('email')) {
                    $utilisateur->setEmail($request->get('email'));
                }
                $errors = $validator->validate($utilisateur);

                if (count($errors) > 0) {
                    $errorTab = [];

                    foreach ($errors as $error) {
                        $errorTab[] = $error->getMessage();
                    }


                    return $send->sendData("", "", 400, $errorTab);
                }
            }
            if ($request->get('motDePasse')) {
                if (strlen($request->get('motDePasse') === 0)) {
                    return $send->sendData("", "", 400, "Mot de passe ne peut etre vide.");
                }
                $utilisateur->setMotDePasse($request->get('motDePasse'));
            }

            if ($request->get('adresse')) {
                if (strlen($request->get('adresse') === 0)) {
                    return $send->sendData("", "", 400, "Adresse ne peut etre vide.");
                }
                $utilisateur->setAdresse($request->get('adresse'));
            }
            if ($request->get('role')) {
                foreach ($utilisateur->getRole() as $role) {
                    $utilisateur->removeRole($role);
                }
                foreach ($request->get('role') as $role) {
                    $utilisateur->addRole($roleRepository->findOneBy(["nom" => $role]));
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $send->sendData(
                $serializer->serialize($utilisateur, 'json', ['groups' => 'get:infoUtilisateur']),
                $links->getEntityLinks($utilisateur->getId(), "PUT", $request->server->get('HTTP_HOST'), 'utilisateur'),
                201,
                "Utilisateur mise à jour"
            );
        } catch (TypeError $e) {
            return $send->sendData("", "", 404, $e->getMessage());
        }
    }
}
