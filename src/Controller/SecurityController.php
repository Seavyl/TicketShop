<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')] // Définit la route 'app_login'
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté (par exemple, via une session active),
        // redirigez-le vers une page appropriée (par exemple, le dashboard admin).
        if ($this->getUser()) {
             return $this->redirectToRoute('admin'); // 'admin' est le nom de la route de votre DashboardController
        }

        // Obtenir l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')] // Définit la route 'app_logout'
    public function logout(): void
    {
        // Cette méthode peut être vide - elle sera interceptée par la clé 'logout' de votre firewall.
        // Symfony gère la logique de déconnexion.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}