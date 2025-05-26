<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Désérialiser les données JSON reçues en objet User
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => 'user:write']);
        } catch (\Exception $e) {
            return $this->json(
                ['message' => 'Données JSON invalides', 'error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Valider l'objet User
        $errors = $validator->validate($user, null, ['user:write']);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier et hasher le mot de passe
        $plaintextPassword = $user->getPassword();
        if (empty($plaintextPassword)) {
            return $this->json(['message' => 'Le mot de passe est obligatoire'], Response::HTTP_BAD_REQUEST);
        }
        $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setPassword($hashedPassword);

        // Définir un rôle par défaut si nécessaire
        if (empty($user->getRole())) {
            $user->setRole(['ROLE_USER']);
        }

        // Sauvegarder l'utilisateur en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Retourner la réponse avec l'utilisateur créé (sans mot de passe)
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }
}