<?php

namespace App\Controller\admin;

use Symfony\Component\Routing\Annotation\Route;

class AdminUserController
{

    #[Route('/admin/logout', name: 'logout')]
    public function logout(){

        //route utilisée par symfony dans le security.yaml
        //c'est normal qu'elle soit vide

    }

}