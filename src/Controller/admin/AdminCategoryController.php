<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\AdminCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{

    #[Route('/admin/categories/create', 'admin_create_category', methods: ['GET', 'POST'])]
    public function createCategory(CategoryRepository $categoryRepository, Request $request, EntityManagerInterface
    $entityManager){

        $category = new Category();

        $adminCategoryForm = $this->createForm(AdminCategoryType::class, $category);

        $adminCategoryForm->handleRequest($request);

        if($adminCategoryForm->isSubmitted() && $adminCategoryForm->isValid()){

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie créée avec succès');

            return $this->redirectToRoute('admin_list_categories');
        }

        $adminCategoryFormView = $adminCategoryForm->createView();

        return $this->render('admin/category/create.html.twig', [
            'adminCategoryFormView' => $adminCategoryFormView,
        ]);
    }

    #[Route('/admin/categories/list', 'admin_list_categories', methods: ['GET'])]
    public function listCategories(CategoryRepository $categoryRepository){

        $categories = $categoryRepository->findAll();

        return $this->render('admin/category/list_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categories/{id}/delete', 'admin_delete_category', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteCategory(int $id, CategoryRepository$categoryRepository, EntityManagerInterface
    $entityManager){

        $category = $categoryRepository->find($id);

        if(!$category){
            return new Response('Pas de catégorie trouvée', 404);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'La catégorie a bien été supprimée');

        return $this->redirectToRoute('admin_list_categories');

    }

    #[Route('/admin/categories/{id}/update', 'admin_update_category', requirements: ['id' => '\d+'], methods: ['GET',
     'POST'])]
    public function updateCategory(int $id, CategoryRepository $categoryRepository, Request $request,
                                   EntityManagerInterface $entityManager){

        $category = $categoryRepository->find($id);

        $adminCategoryForm = $this->createForm(AdminCategoryType::class, $category);

        $adminCategoryForm->handleRequest($request);

        if($adminCategoryForm->isSubmitted() && $adminCategoryForm->isValid()){

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie modifiée');

            return $this->redirectToRoute('admin_list_category');
        }

        $adminCategoryFormView = $adminCategoryForm->createView();

        return $this->render('admin/category/update_category.html.twig', [
            'adminCategoryFormView' => $adminCategoryFormView,
            'categories' => $category
        ]);



    }
}