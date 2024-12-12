<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{

    //je créé une fonction d'authentification grâce â la méthode
    //AuthenticationUtils fournie par Symfony
    //je créé une variable error qui permet d'afficher les erreurs quand on tape l'identifiant ou le mot de passe
    //je créé une variable lastUsername pour récupérer le dernier username entré par l'utilisateur


    //j'ai créé mon formulaire twig dans mon dossier public pour y avoir accès sur les pages public du site
    //j'ai aussi modifié mon dossier de config security.yaml (je ne sais pas rééllemnt pourquoi...)
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response{

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('public/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);

    }
}
