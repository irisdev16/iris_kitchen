<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use App\Repository\RecipeRepository;
use App\service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{
    #[Route('/admin/recipes/create', 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function createRecipe(UniqueFilenameGenerator $uniqueFilenameGenerator, RecipeRepository $recipeRepository,
                                 Request $request,
EntityManagerInterface
    $entityManager, ParameterBagInterface $parameterBag)
    {
        $recipe = new Recipe();

        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        $adminRecipeForm->handleRequest($request);

        if ($adminRecipeForm->isSubmitted() && $adminRecipeForm->isValid()) {

            // je récupère le fichier envoyé dans le champs image du formulaire
            $recipeImage = $adminRecipeForm->get('image')->getData();


            // s'il y a bien une image envoyée
            if ($recipeImage) {

                //recupère le nom original de l'image (exemple : poulet.png)
                $imageOriginalName = $recipeImage -> getClientOriginalName();

                //ensuite je veux récupérer l'extension (png, jped, etc) de l'image
                $imageExtension = $recipeImage -> guessExtension();
                //j'ai créé un controlleur UniqueFilenameGenerator dans un dossier service
                //ici grâce ) l'auto-wire, je récupère ma classe UniqueFilenameGenerator dans les paramètre de la
                // function (plus haut) et ça me permet de récupérer la méthod generateUniqueFilename
                // avec $uniqueFilenameGenerator
                //je n'oublie pas de lui passer en paramètre le nom original de l'image que je veux modifier et son
                // extension (jpg, jpeg, etc)
                $imageNewFilename = $uniqueFilenameGenerator ->generateUniqueFilename($imageOriginalName,
                    $imageExtension);

                // je récupère grâce à la classe ParameterBag, le chemin
                // vers la racine du projet
                $rootDir = $parameterBag->get('kernel.project_dir');
                // je génère le chemin vers le dossier uploads (dans le dossier public)
                $uploadsDir = $rootDir . '/public/assets/uploads';

                // je déplace mon image dans le dossier uploads, en lui donnant
                // le nom unique
                $recipeImage->move($uploadsDir, $imageNewFilename);

                // je stocke dans l'entité le nouveau nom de l'image
                $recipe->setImage($imageNewFilename);
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
            'recipes' => $recipes
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
    public function updateRecipe(int $id, UniqueFilenameGenerator $uniqueFilenameGenerator,RecipeRepository
    $recipeRepository, Request
$request,
                                 EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $recipe = $recipeRepository->find($id);

        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        $adminRecipeForm->handleRequest($request);

        if ($adminRecipeForm->isSubmitted()) {

            $recipeImage = $adminRecipeForm->get('image')->getData();

            if ($recipeImage) {
                //recupère le nom original de l'image (exemple : poulet.png)
                $imageOriginalName = $recipeImage -> getClientOriginalName();

                //ensuite je veux récupérer l'extension (png, jped, etc) de l'image
                $imageExtension = $recipeImage -> guessExtension();
                //j'ai créé un controlleur UniqueFilenameGenerator dans un dossier service
                //ici grâce ) l'auto-wire, je récupère ma classe UniqueFilenameGenerator dans les paramètre de la
                // function (plus haut) et ça me permet de récupérer la méthod generateUniqueFilename
                // avec $uniqueFilenameGenerator
                //je n'oublie pas de lui passer en paramètre le nom original de l'image que je veux modifier et son
                // extension (jpg, jpeg, etc)
                $imageNewFilename = $uniqueFilenameGenerator ->generateUniqueFilename($imageOriginalName,
                    $imageExtension);

                $rootDir = $parameterBag->get('kernel.project_dir');
                $uploadsDir = $rootDir . '/public/assets/uploads';
                $recipeImage->move($uploadsDir, $imageNewFilename);

                $recipe->setImage($imageNewFilename);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recette modifiée');
        }

        $adminRecipeFormView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/update_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
            'recipes' => $recipe
        ]);
    }

}