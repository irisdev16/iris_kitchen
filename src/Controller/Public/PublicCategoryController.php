<?php

namespace App\Controller\Public;

use App\Repository\CategoryRepository;
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

    #[Route('/categories/{id}', 'show_category',  methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showCategory(int $id, CategoryRepository $categoryRepository){

        $category = $categoryRepository->find($id);

        if (!$category) {
            $notFoundResponse = new Response('Recette non trouvée', 404);
            return $notFoundResponse;
        }

        return $this->render('public/categories/show_category.html.twig', [
            'category' => $category
        ]);
    }
}