<?php

namespace App\Controller\Public;

use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicCategoryController extends AbstractController
{

    #[Route('/categories', 'list_categories', methods: ['GET'])]
    public function listCategories(CategoryRepository $categoryRepository){

        $categories = $categoryRepository->findAll();

        return $this->render('public/category/list_categories.html.twig', [
            'categories' => $categories
        ]);
    }



    #[Route('/categories/{id}', 'show_category', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showCategory(int $id, CategoryRepository $categoryRepository,RecipeRepository
                                         $recipeRepository){

        $category = $categoryRepository->find($id);

        $recipes = $recipeRepository->findBy(['category' => $category]);

        return $this->render('public/category/show_category.html.twig', [
            'recipes' => $recipes,
            'category' => $category
        ]);

    }
}