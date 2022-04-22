<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\RechercheType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur le blog',
            'age' => 27
        ]);
        // pour envoyer des variables à une vue, on les passe dans un tableau associatif
        // indice => valeur
    }

    /**
     * @Route("/blog", name="app_blog")
     */
    // une route est composée de deux arguments : le chemin et le nom
    // chaque route lance la méthode située juste en dessous
    public function index(ArticleRepository $repo, Request $request): Response
    {
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())    // si on fait une recherche
        {
            $data = $form->get('recherche')->getData(); // je récupère la saisie de l'utilisateur
            $articles = $repo->getArticlesByName($data);
        }
        else    // pas de recherche : on récupère tous les articles
        {
            $articles = $repo->findAll();
        }

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
            'formRecherche' => $form->createView()
        ]);
        // la méthode render() qui permet d'afficher un template
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    // route paramétrée
    public function show(Article $article)
    {
        // grâce au ParamConverter, Symfony va essayer de récupérer un objet Article correspondant à l'id dans la route
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);   
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/edit/{id}", name="blog_edit")
     */
    public function form(Request $request, EntityManagerInterface $manager, Article $article = null)
    {
        // la classe Request contient toutes les données des superglobales
        // dump($request);
        if(!$article)
        {
            $article = new Article;
            $article->setCreatedAt(new \DateTime());    // je lui donne une date à l'insertion
        }
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        dump($article);
        // handleRequest() permet de faire des vérifications sur le form (méthode du formulaire ? est-ce que les champs sont remplis ?)
        // permet aussi de remplir l'objet $article avec les données du form

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($article);    // préparer l'insertion de l'article en bdd
            $manager->flush();  // insère l'article
            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render("blog/form.html.twig", [
            'editMode' => $article->getId() !== null,
            'formArticle' => $form->createView()    // createView() renvoie un objet pour afficher le formulaire
        ]);
    }
}
