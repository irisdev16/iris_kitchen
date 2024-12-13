<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\UsersType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{

    #[Route('/admin/logout', name: 'logout', methods: ['GET'])]
    public function logout(){

        //route utilisée par symfony dans le security.yaml
        //c'est normal qu'elle soit vide

    }

    #[Route('/admin/users/list', name: 'admin_list_users', methods: ['GET'])]
    public function listAdmins(UserRepository $userRepository){

        $admins = $userRepository->findAll();

        return $this->render('admin/list_users.html.twig', [
            'admins' => $admins,
        ]);
    }


    #[Route('/admin/users/create', name: 'admin_create_user', methods: ['GET', 'POST'])]
    public function createUser(Request $request, EntityManagerInterface
    $entityManager, UserPasswordHasherInterface $userPasswordHasher) {

        //dd('hello');

        $user = new User();

        $userForm = $this->createForm(UsersType::class, $user);

        //dd($userForm);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $password = $userForm->get('password')->getData();

            $hashedPassword = $userPasswordHasher->hashPassword($user, $password);

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User créé !!');

            //return $this->redirectToRoute('admin_create_user');
        }

        $userFormView = $userForm->createView();

        return $this->render('admin/create_user.html.twig', [
            'userFormView' => $userFormView,
        ]);
    }

    #[Route('admin/users/{id}/delete', name: 'admin_delete_user', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager){

        $user = $userRepository->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur bien supprimé');

        return $this->redirectToRoute('admin_list_users');

    }
}