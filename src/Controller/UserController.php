<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Pour Symfony 5.3+
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // Pour les versions antérieures à 5.3

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher // Injecter le service de hachage
        // UserPasswordEncoderInterface $passwordEncoder // Pour les versions antérieures
    ): JsonResponse {
        // 1. Désérialiser les données JSON en une entité User
        try {
            // Utilisez le groupe 'user:write' pour la désérialisation
            $user = $serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => 'user:write']);
        } catch (\Throwable $e) {
            return $this->json(
                ['message' => 'Données JSON invalides', 'error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST // 400
            );
        }

        // 2. Valider l'entité User
        // Utilisez le groupe de validation 'user:write' (assurez-vous que vos @Assert ont ce groupe)
        $errors = $validator->validate($user, null, ['user:write']);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST // 400
            );
        }

        // 3. Hasher le mot de passe
        // Important : Assurez-vous que getPassword() ne retourne pas null ou vide ici
        $plaintextPassword = $user->getPassword();
        if ($plaintextPassword) {
             $hashedPassword = $passwordHasher->hashPassword(
                $user, // L'objet User
                $plaintextPassword
            );
            // $hashedPassword = $passwordEncoder->encodePassword($user, $plaintextPassword); // Pour les versions antérieures
            $user->setPassword($hashedPassword); // Mettre le mot de passe hashé sur l'entité
        } else {
             // Gérer le cas où le mot de passe est manquant (devrait être attrapé par la validation NotBlank)
             // Mais une sécurité supplémentaire ne fait pas de mal
             return $this->json(
                ['message' => 'Le mot de passe est obligatoire'],
                Response::HTTP_BAD_REQUEST
            );
        }


        // 4. Attribuer un rôle par défaut (si nécessaire)
        // Par défaut, les utilisateurs enregistrés peuvent avoir ROLE_USER
        if (empty($user->getRole())) {
             $user->setRole(['ROLE_USER']);
        }


        // 5. Persister l'utilisateur en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // 6. Réponse de succès
        // Vous pourriez vouloir retourner les informations de l'utilisateur créé (hors mot de passe)
        // ou simplement un message de succès.
        // Utilisez le groupe 'user:read' pour serialiser l'objet user avant de le retourner
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true); // 201 Created
    }

    // Vous pourriez ajouter d'autres actions liées à la gestion du compte utilisateur si nécessaire
    // (par exemple, demande de réinitialisation de mot de passe, validation d'email)
}
