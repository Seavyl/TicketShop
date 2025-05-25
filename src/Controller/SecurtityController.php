<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; // Non nécessaire pour l'API JWT standard

class SecurityController extends AbstractController
{
    /**
     * Ce endpoint est intercepté par le firewall JWT.
     * Il reçoit le JSON avec email/password et retourne un JWT.
     */
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(): JsonResponse
    {
        // La logique d'authentification et la génération du token sont gérées par le bundle.
        // Cette méthode est là pour définir la route. Le code n'est généralement pas exécuté en cas de succès.
        // En cas d'échec, le bundle retourne une 401.

        throw new \LogicException('This method should not be reached if the firewall is configured correctly.');

        /*
        // Si vous aviez une logique très spécifique après le succès (rare avec ce setup),
        // elle se situerait DANS VOTRE CONFIGURATION security.yaml (via success_handler)
        // ou dans un Event Listener lié à l'authentification réussie.
        */
    }

    /**
     * Un exemple de endpoint sécurisé pour tester l'authentification.
     * Requiert un JWT valide dans l'en-tête Authorization.
     */
    #[Route('/api/profile', name: 'api_profile', methods: ['GET'])]
    // N'oubliez pas d'ajouter la sécurité nécessaire ici (par exemple, is_granted('IS_AUTHENTICATED_FULLY'))
    public function apiProfile(): JsonResponse
    {
        // $this->getUser() fonctionne si le JWT est valide
        $user = $this->getUser();

        if (!$user) {
             // En théorie, le firewall devrait déjà empêcher l'accès si non authentifié
             return $this->json(['message' => 'Non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Retourner des informations utilisateur (sans le mot de passe)
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            // ... autres infos
        ]);
    }

    // Vous pourriez ajouter une action pour la révocation de token si nécessaire (POST /api/logout ou DELETE /api/tokens/{tokenId})
    // #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    // public function logoutApi(): JsonResponse
    // {
    //     // Logique de révocation de token, si implémentée
    //     // Le bundle JWT ne gère pas cela par défaut
    //     return $this->json(['message' => 'Déconnexion réussie (le token reste valide jusqu\'à expiration, sauf si révoqué)']);
    // }
}
