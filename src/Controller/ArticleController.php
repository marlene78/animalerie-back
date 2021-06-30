<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\SendDataController;
use TypeError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Article;
use App\Repository\UtilisateurRepository;
use Exception;

/**
 * @Route("/article")
 */

class ArticleController extends AbstractController
{




    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository, SendDataController $send,  SerializerInterface $serializer): JsonResponse
    {

        try {
            $articles = $articleRepository->findAll();
            return $send->sendData(
                $serializer->serialize($articleRepository->findAll(), 'json', ['groups' => 'get:infoArticle']),
                $this->getEntityLinks(),
                200,
                (count($articles) > 0) ? 'Article trouvés' : 'Aucun article'
            );
        } catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }
    }





    /**
     * @Route("/new", name="article_new", methods={"POST"})
     */
    public function new(Request $request ,ValidatorInterface $validator ,SendDataController $send ,  SerializerInterface $serializer, UtilisateurRepository $utilisateurRepository):JsonResponse
    {
        try{
            if(!$request->get("auteur_id") || !$request->get("titre") || !$request->get("contenu")){
                return $send->sendData("", "",400, "Paramètre manquant");
            }
            $article = new Article();
            $article->setAuteur($utilisateurRepository->find($request->get("auteur_id")));
            $article->setTitre($request->get("titre"));
            $article->setContenu($request->get("contenu"));
            $errors = $validator->validate($article);

               if (count($errors) > 0) {
                $errorTab = []; 

                foreach ($errors as $error ) {
                    $errorTab[] = $error->getMessage();
                }

                return $send->sendData("", "",400, $errorTab);


            }

            $em = $this->getDoctrine()->getManager(); 
                $em->persist($article); 
                $em->flush();

                return $send->sendData(
                    $serializer->serialize($article,'json',['groups' => 'get:infoArticle']), 
                    $this->getEntityLinks(),
                    200,
                    "Article ajouté"
                );


        }catch(TypeError $e){
            return $this->json($e->getMessage() , 400);
        }


    }





    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show( SendDataController $send ,  SerializerInterface $serializer, Request $request, ArticleRepository $articleRepository ):JsonResponse
    { 

        $article = $articleRepository->find($request->get('id'));
        return $send->sendData(
            $serializer->serialize($article,'json',['groups' => 'get:infoArticle']), 
            $this->getEntityLinks(),
            ($article) ? 200 : 404,
            ($article) ? "Article trouvée" : "Article non trouvée"
        );       
    }





    /**
     * @Route("/{id}/edit", name="article_edit", methods={"PUT"})
     */
    public function edit(Request $request,Article $article, ValidatorInterface $validator , SendDataController $send ,  SerializerInterface $serializer, ArticleRepository $articleRepository):JsonResponse
    {

        try{

     

            if(!$request->get('contenu') &&  !$request->get('titre')){
                return $send->sendData("", "",400, "Paramètre manquant");
            }

            $article->setContenu(($request->get('contenu') ? $request->get('contenu') : $articleFound->getContenu()));
           
            if($request->get('titre')){
                $article->setTitre($request->get('titre'));
                $errors = $validator->validate($article);

                if (count($errors) > 0) {
                 $errorTab = []; 
 
                 foreach ($errors as $error ) {
                     $errorTab[] = $error->getMessage();
                 }
    
 
                 return $send->sendData("", "",400, $errorTab);
            }
        }

                $em = $this->getDoctrine()->getManager(); 
                $em->flush(); 

                return $send->sendData(
                    $serializer->serialize($article,'json',['groups' => 'get:infoArticle']), 
                    $this->getEntityLinks(),
                    200,
                    "Article mise à jour"
                );


            }
        catch(TypeError $e){
            return $send->sendData("", "",404,$e->getMessage());
        }
    }







    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Article $article , SendDataController $send ): JsonResponse
    {
        try{

            $entityManager = $this->getDoctrine()->getManager();


            $entityManager->remove($article);
            $entityManager->flush();

            return $send->sendData("", $this->getEntityLinks(),201,"Article supprimée");

        }catch(TypeError $e){
            return $send->sendData("", "",400,$e->getMessage());
        }

    }







    /**
     * Renvoi la liste des links
     * @return array
     */
    public function getEntityLinks()
    {
        return [
            "GET" => "localhost:5000/animaux",
            "GET" => "localhost:5000/animaux/{id}/",
            "POST" => "localhost:5000/animaux/new",
            "PUT" => "localhost:5000/animaux/{id}/edit",
            "DELETE" => "localhost:5000/animaux/{id}"
        ];
    }
}
