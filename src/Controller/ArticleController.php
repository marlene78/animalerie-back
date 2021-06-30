<?php

namespace App\Controller;

use TypeError;
use App\Entity\Article;
use App\Services\EntityLinks;
use App\Services\SendDataController;
use App\Repository\ArticleRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/article")
 */

class ArticleController extends AbstractController
{
    function checkIfUserIsEditeur($roles): bool
    {
        foreach ($roles as $role) {
            if ($role->getNom() === "editeur") {
                return true;
            }
        }
        return false;
    }
    /**
     * Afficher la liste des articles
     * @Route("/", name="article_index", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @param SendDataController $send,  
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    public function index(
        ArticleRepository $articleRepository,
        SendDataController $send,
        SerializerInterface $serializer,
        Request $request
    ): JsonResponse {

        try {
            $articles = $articleRepository->findAll();
            return $send->sendData(
                $serializer->serialize($articleRepository->findAll(), 'json', ['groups' => 'get:infoArticle']),
                ["POST" => "" . $request->server->get('HTTP_HOST') . "/article/new"],
                200,
                (count($articles) > 0) ? 'Article trouvés' : 'Aucun article'
            );
        } catch (TypeError $e) {
            return $this->json($e->getMessage(), 400);
        }
    }





    /**
     * Création d'un article
     * @Route("/new", name="article_new", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SendDataController $send
     * @param  SerializerInterface $serializer
     * @param  UtilisateurRepository $utilisateurRepository
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function new(
        Request $request,
        ValidatorInterface $validator,
        SendDataController $send,
        SerializerInterface $serializer,
        UtilisateurRepository $utilisateurRepository,
        EntityLinks $links,
    ): JsonResponse {


        try {
            if (!$request->get("user_id") || !$request->get("titre") || !$request->get("contenu")) {
                return $send->sendData("", "", 400, "Paramètre manquant");
            }
            $utilisateur = $utilisateurRepository->find($request->get('user_id'));

            if (!$this->checkIfUserIsEditeur($utilisateur->getRole())) {
                return $send->sendData("", "", 400, "L'utilisateur n'est pas un auteur");
            }
            $article = new Article();
            $article->setAuteur($utilisateurRepository->find($request->get("user_id")));
            $article->setTitre($request->get("titre"));
            $article->setContenu($request->get("contenu"));
            $errors = $validator->validate($article);

            if (count($errors) > 0) {
                $errorTab = [];

                foreach ($errors as $error) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "", 400, $errorTab);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $send->sendData(
                $serializer->serialize($article, 'json', ['groups' => 'get:infoArticle']),
                $links->getEntityLinks($article->getId(), "POST", $request->server->get('HTTP_HOST'), "article"),
                201,
                "Article ajouté"
            );
        } catch (TypeError $e) {
            return $this->json($e->getMessage(), 400);
        }
    }





    /**
     * Affichage d'un article suivant son id 
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @param SendDataController $send 
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param  EntityLinks $links
     * @return JsonResponse
     */
    public function show(
        SendDataController $send,
        SerializerInterface $serializer,
        Request $request,
        Article $article,
        EntityLinks $links
    ): JsonResponse {
        try {
            return $send->sendData(
                $serializer->serialize($article, 'json', ['groups' => 'get:infoArticle']),
                $links->getEntityLinks($article->getId(), "GET", $request->server->get('HTTP_HOST'), "article"),
                ($article) ? 200 : 404,
                ($article) ? "Article trouvée" : "Article non trouvée"
            );
        } catch (TypeError $e) {
            return $this->json($e->getMessage(), 400);
        }
    }





    /**
     * Edition d'un article en fonction de son ID
     * @Route("/{id}/edit", name="article_edit", methods={"PUT"})
     * @param Request $request, 
     * @param Article $article, 
     * @param ValidatorInterface $validator, 
     * @param SendDataController $send,  
     * @param SerializerInterface $serializer, 
     * @param EntityLinks $links
     * @return JsonResponse
     */
    public function edit(
        Request $request,
        Article $article,
        ValidatorInterface $validator,
        SendDataController $send,
        SerializerInterface $serializer,
        EntityLinks $links,
        UtilisateurRepository $utilisateurRepository
    ): JsonResponse {

        try {



            if (!$request->get('contenu') &&  !$request->get('titre') || !$request->get('user_id')) {
                return $send->sendData("", "", 400, "Paramètre manquant");
            }
            if ($request->get('user_id') != $article->getAuteur()->getId()) {
                return $send->sendData("", "", 400, "Utilisateur n'est pas l'auteur de l'article");
            }
            $utilisateur = $utilisateurRepository->find($request->get('user_id'));

            if (!$this->checkIfUserIsEditeur($utilisateur->getRole())) {
                return $send->sendData("", "", 400, "L'utilisateur n'est pas un auteur");
            }

            $article->setContenu(($request->get('contenu') ? $request->get('contenu') : $article->getContenu()));

            if ($request->get('titre')) {
                $article->setTitre($request->get('titre'));
                $errors = $validator->validate($article);

                if (count($errors) > 0) {
                    $errorTab = [];

                    foreach ($errors as $error) {
                        $errorTab[] = $error->getMessage();
                    }


                    return $send->sendData("", "", 400, $errorTab);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $send->sendData(
                $serializer->serialize($article, 'json', ['groups' => 'get:infoArticle']),
                $links->getEntityLinks($article->getId(), "PUT", $request->server->get('HTTP_HOST'), 'article'),
                201,
                "Article mise à jour"
            );
        } catch (TypeError $e) {
            return $send->sendData("", "", 404, $e->getMessage());
        }
    }







    /**
     * Supprimer un article en fonction de son ID
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @param Article $article,
     * @param SendDataController $send
     * @return JsonResponse 
     */
    public function delete(
        Article $article,
        SendDataController $send
    ): JsonResponse {
        try {

            $entityManager = $this->getDoctrine()->getManager();


            $entityManager->remove($article);
            $entityManager->flush();

            return $send->sendData("", "", 201, "Article supprimée");
        } catch (TypeError $e) {
            return $send->sendData("", "", 400, $e->getMessage());
        }
    }
}
