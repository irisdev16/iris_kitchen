<?php

namespace App\Controller\Public;

use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicRecipeController extends AbstractController
{
    #[Route('/recipes', 'list_recipes', methods: ['GET'])]
    public function listPublishedRecipes(RecipeRepository $recipeRepository)
    {
        $publishedRecipes = $recipeRepository->findBy(['isPublished' => true]);

        return $this->render('public/recipe/list_recipe.html.twig', [
            'publishedRecipes' => $publishedRecipes
        ]);

    }

    #[Route('/recipes/{id}', 'show_recipe',  methods: ['GET'], requirements: ['id' => '\d+'])]

    public function showRecipe(int $id, RecipeRepository $recipeRepository) {


        $recipe = $recipeRepository->find($id);

        if (!$recipe || !$recipe->isPublished()) {
            $notFoundResponse = new Response('Recette non trouvée', 404);
            return $notFoundResponse;
        }

        return $this->render('public/recipe/show_recipe.html.twig', [
            'recipe' => $recipe
        ]);
    }

    //je créé une fonction search afin de permettre à l'utilisateur de recherche dans une barre de recherche une recette
    //as usual j'indique la route avec la méthode GET
    //je passe la méthode Request et le Repository dans les paramètre de ma méthode
    // j'ai créé une fonction findBySearchInTitle dans mon RecipeRepository
    //la variable $search utilise la méthode request et appelle le queryBuilder avec getQuery avec paramètre 'search'
    //ma variable $recipe appelle le RecipeRepository et plus précisément la function appelée findBySearchInTitle et
    // lui passe la variable $search
    //return une vue twig
    #[Route ('recipes/search', 'search_recipes', methods: ['GET'])]
    public function searchRecipes(Request $request, RecipeRepository $recipeRepository) {

        $search = $request->query->get('search');

        $recipe = $recipeRepository->findBySearchInTitle($search);

        return $this->render('public/recipe/search_recipe.html.twig', [
            'recipes' => $recipe,
            'search' => $search
        ]);
    }



}