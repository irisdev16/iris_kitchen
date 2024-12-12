<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{
    #[Route('/admin/recipes/create', 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function createRecipe(RecipeRepository $recipeRepository,Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $recipe = new Recipe();

        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        $adminRecipeForm->handleRequest($request);

        if ($adminRecipeForm->isSubmitted()) {

            // je récupère le fichier envoyé dans le champs image du formulaire
            $recipeImage = $adminRecipeForm->get('image')->getData();

            // s'il y a bien une image envoyée
            if ($recipeImage) {

                // je génère un nom unique pour l'image, en gardant l'extension
                // originale (.jpeg, .png etc)
                $imageNewName = uniqid() . '.' . $recipeImage->guessExtension();

                // je récupère grâce à la classe ParameterBag, le chemin
                // vers la racine du projet
                $rootDir = $parameterBag->get('kernel.project_dir');
                // je génère le chemin vers le dossier uploads (dans le dossier public)
                $uploadsDir = $rootDir . '/public/assets/uploads';

                // je déplace mon image dans le dossier uploads, en lui donnant
                // le nom unique
                $recipeImage->move($uploadsDir, $imageNewName);

                // je stocke dans l'entité le nouveau nom de l'image
                $recipe->setImage($imageNewName);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recette enregistrée');
        }

        $adminRecipeFormView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/create_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView
        ]);

    }

    #[Route('/admin/recipes/list', 'admin_list_recipes', methods: ['GET'])]
    public function listRecipes(RecipeRepository $recipeRepository) {

        $recipes = $recipeRepository->findAll();

        return $this->render('admin/recipe/list_recipes.html.twig', [
            'recipe' => $recipes
        ]);

    }

    #[Route('/admin/recipes/{id}/delete', 'admin_delete_recipe', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteRecipe(int $id, RecipeRepository $recipeRepository, EntityManagerInterface $entityManager)
    {
        $recipe = $recipeRepository->find($id);

        $entityManager->remove($recipe);
        $entityManager->flush();

        $this->addFlash('success', "La recette a bien été supprimée");

        return $this->redirectToRoute("admin_list_recipes");
    }

    #[Route('/admin/recipes/{id}/update', 'admin_update_recipe', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateRecipe(int $id, RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $recipe = $recipeRepository->find($id);

        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        $adminRecipeForm->handleRequest($request);

        if ($adminRecipeForm->isSubmitted()) {

            $recipeImage = $adminRecipeForm->get('image')->getData();

            if ($recipeImage) {
                $imageNewName = uniqid() . '.' . $recipeImage->guessExtension();

                $rootDir = $parameterBag->get('kernel.project_dir');
                $uploadsDir = $rootDir . '/public/assets/uploads';
                $recipeImage->move($uploadsDir, $imageNewName);

                $recipe->setImage($imageNewName);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recette modifiée');
        }

        $adminRecipeFormView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/update_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
            'recipe' => $recipe
        ]);
    }

}