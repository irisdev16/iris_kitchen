<?php

namespace App\Controller\admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminListCurrentAdmins extends AbstractController
{

    #[Route('/admin/users/list', name: 'admin_list_users')]
    public function listAdmins(UserRepository $userRepository){

        $users = $userRepository->findAll();

        $admins = array_filter($users, function ($user){
            return in_array ('ROLE_ADMIN', $user->getRoles());
        });

        return $this->render('admin/list_users.html.twig', [
            'admins' => $admins,
        ]);
    }
}