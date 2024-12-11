<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe')]
    public function index(): Response
    {
        return $this->render('admin/recipe/index.html.twig');
    }


    //je créé une méthode de création de recettes
    //je n'oublie pas de créer ma route avant afin de récupérer les infos d'url comme indiqué ici et récupérer aussi
    // la méthode POST et GET
    #[Route('/admin/recipe/create', name: 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function adminCreateRecipe(){

        //je créé une variable $recipe qui contient une nouvelle instance de la classe Recipe
        $recipe = new Recipe();
        // je créé le formulaire via la méthode createForm qui prend en paramètre mon form AdminRecipeType ainsi que
        // la variable $recipe qui contient mon instance de la classe Recipe
        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        //Je créé une vue pour mon formulaire avec la méthode createView :
        //ma variable $adminRecipeFormView récupère mon formulaire juste au dessus avec $adminRecipeForm et applique
        // la méthode createView a ce formulaire
        $adminRecipeFormView = $adminRecipeForm->createView();

        //je retourne un vue twig grâce a la méthode render, je lui indique le chemin de mon template twig
        //je lui passe en paramètres a gauche entre simplequote ce que j'appelerai dans mon twig et a droite la
        // variable qui lui est associée
        return $this->render('admin/recipe/create_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
        ]);

    }
}
