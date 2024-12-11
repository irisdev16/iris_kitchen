<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use App\Repository\RecipeRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipes')]
    public function index(RecipeRepository $recipeRepository): Response
    {

        $recipes = $recipeRepository->findAll();
        return $this->render('admin/recipe/index.html.twig',[
            'recipes' => $recipes,
        ]);
    }


    //je créé une méthode de création de recettes qui permettra a l'admin de créer une recette via un formulaire
    //j'utilise des fonctionnalité de symfony qui me permettront justement de faciliter la création de recettes
    //je n'oublie pas de créer ma route avant afin de récupérer les infos d'url comme indiqué ici et récupérer aussi
    // la méthode POST et GET
    #[Route('/admin/recipe/create', name: 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function adminCreateRecipe(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag):
    Response{

        //je créé une variable $recipe qui contient une nouvelle instance de la classe Recipe
        $recipe = new Recipe();
        // je créé le formulaire via la méthode createForm qui prend en paramètre mon form AdminRecipeType ainsi que
        // la variable $recipe qui contient mon instance de la classe Recipe
        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        //j'utilise la méthode handleRequest, instanciéé par symfony
        //le handleRequest récupère les données de POST (donc du form envoyé)
        //pour chaque donné il modifie l'entité (le titre, l'image, etc)
        //il va remplir l'entité créée par new Recipe avec les données du formulaire rempli
        $adminRecipeForm->handleRequest($request);
        if($adminRecipeForm->isSubmitted()){

            $recipeImage = $adminRecipeForm->get ('image')->getData();

            //s'il a bien une image envoyée
            //alors je lui donne un identifiant unique, un nom unique pour l'image en gardant l'extension originale(.jpg, .png, etc)
            //je récupère grace a la classe parameterBag le chemin vers la racine du projet
            //je génère le chemin vers le dossier images dans le dossier public
            //je déplace (move) mon image dans le dossier images en la renommant avec l'identifiant unique
            //j stock dans l'entité le nouveau nom de l'image
            if($recipeImage){

                $imageNewName = md5(uniqid()).'.'.$recipeImage->guessExtension();

                $rootDir = $parameterBag->get('kernel.project_dir');
                $imagesDir = $rootDir.'/public//assets/images';
                $recipeImage->move($imagesDir, $imageNewName);

                $recipe->setImage($imageNewName);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();
            $this->addFlash('success', 'Recette créée avec success');

            return $this->redirectToRoute('admin_create_recipe');
        }


        //Je créé une vue pour mon formulaire avec la méthode createView :
        //ma variable $adminRecipeFormView récupère mon formulaire juste au dessus avec $adminRecipeForm et applique
        // la méthode createView a ce formulaire
        $adminRecipeFormView = $adminRecipeForm->createView();

        //je retourne un vue twig grâce a la méthode render, je lui indique le chemin de mon template twig
        //je lui passe en paramètres a gauche entre simplequote ce que j'appelerai dans mon twig et a droite la
        // variable qui lui est associée
        return $this->render('admin/recipe/create_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
            'recipe' => $recipe
        ]);
    }

    //je créé une méthode qui me permettra d'afficher toutes mes recettes
    #[Route ('/admin/recipes/list', 'admin_list_recipes',methods: ['GET', 'POST'] )]
    public function listRecipes(RecipeRepository $recipeRepository): Response{

        $recipes = $recipeRepository->findAll();

    return $this->render('admin/recipe/list_recipes.html.twig', [
        'recipes' => $recipes,
    ]);

    }

    #[Route ('recipe/{id}', name: 'recipe_show')]
    public function show(int $id, RecipeRepository $recipeRepository): Response{

        $recipe = $recipeRepository->find($id);

        return $this->render('admin/recipe/show.html.twig',[
            'recipe' => $recipe,
        ]);
    }

    #[Route('/admin/recipes/{id}/delete', name: 'admin_delete_recipe', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'] )]
    public function deleteRecipe(int $id, RecipeRepository $recipeRepository, EntityManagerInterface $entityManager): Response{

        $recipe = $recipeRepository->find($id);

        $entityManager->remove($recipe);
        $entityManager->flush();

        $this->addFlash('success','Recette supprimée avec succès');
        return $this->redirectToRoute('recipes');


    }
}
