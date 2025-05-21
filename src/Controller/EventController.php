<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface; // Pour la sérialisation JSON
use Symfony\Component\Validator\Validator\ValidatorInterface; // Pour la validation des données

#[Route('/api/events')]
final class EventController extends AbstractController
{
    

    /**
     * Liste de tous les événements.
     * GET /api/events
     */
    #[Route('', name: 'api_events_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository, SerializerInterface $serializer): JsonResponse // Services injectés ici
    {
        $events = $eventRepository->findAll();

        // Utilisation de la variable $serializer injectée
        $jsonEvents = $serializer->serialize($events, 'json', ['groups' => 'event:list']);

        return new JsonResponse($jsonEvents, Response::HTTP_OK, [], true);
    }

    /**
     * Affiche les détails d'un événement spécifique.
     * GET /api/events/{id}
     */
    #[Route('/{id}', name: 'api_events_show', methods: ['GET'])]
    public function show(int $id, EventRepository $eventRepository, SerializerInterface $serializer): JsonResponse // Services injectés ici
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            return $this->json(
                ['message' => 'Événement non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Utilisation de la variable $serializer injectée
        $jsonEvent = $serializer->serialize($event, 'json', ['groups' => 'event:read']);

        return new JsonResponse($jsonEvent, Response::HTTP_OK, [], true);
    }

    /**
     * Crée un nouvel événement.
     * POST /api/events
     */
    #[Route('', name: 'api_events_create', methods: ['POST'])]
    public function create(
        Request $request,
        SerializerInterface $serializer, // Service injecté
        ValidatorInterface $validator, // Service injecté
        EntityManagerInterface $entityManager // Service injecté
    ): JsonResponse {
        // Utilisation de la variable $serializer injectée
        try {
            $event = $serializer->deserialize($request->getContent(), Event::class, 'json', ['groups' => 'event:write']);
        } catch (\Throwable $e) {
            return $this->json(
                ['message' => 'Données JSON invalides', 'error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Utilisation de la variable $validator injectée
        $errors = $validator->validate($event, null, ['event:write']);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Utilisation de la variable $entityManager injectée
        $entityManager->persist($event);
        $entityManager->flush();

        // Utilisation de la variable $serializer injectée
        $jsonEvent = $serializer->serialize($event, 'json', ['groups' => 'event:read']);

        return new JsonResponse($jsonEvent, Response::HTTP_CREATED, [], true);
    }

    /**
     * Met à jour un événement existant.
     * PUT /api/events/{id}
     */
    #[Route('/{id}', name: 'api_events_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        EventRepository $eventRepository, // Service injecté
        SerializerInterface $serializer, // Service injecté
        ValidatorInterface $validator, // Service injecté
        EntityManagerInterface $entityManager // Service injecté
    ): JsonResponse {
        // Utilisation de la variable $eventRepository injectée
        $event = $eventRepository->find($id);

        if (!$event) {
            return $this->json(
                ['message' => 'Événement non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Utilisation de la variable $serializer injectée
        try {
             $serializer->deserialize( // Utilisez $serializer ici
                $request->getContent(),
                Event::class,
                'json',
                [
                    'groups' => 'event:write',
                    'object_to_populate' => $event
                ]
            );
        } catch (\Throwable $e) {
             return $this->json(
                ['message' => 'Données JSON invalides', 'error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }


        // Utilisation de la variable $validator injectée
        $errors = $validator->validate($event, null, ['event:write']);

        if (count($errors) > 0) {
             $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Utilisation de la variable $entityManager injectée
        $entityManager->flush();

        // Utilisation de la variable $serializer injectée
        $jsonEvent = $serializer->serialize($event, 'json', ['groups' => 'event:read']);

        return new JsonResponse($jsonEvent, Response::HTTP_OK, [], true);
    }

    /**
     * Supprime un événement.
     * DELETE /api/events/{id}
     */
    #[Route('/{id}', name: 'api_events_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        EventRepository $eventRepository, // Service injecté
        EntityManagerInterface $entityManager // Service injecté
    ): JsonResponse {
        // Utilisation de la variable $eventRepository injectée
        $event = $eventRepository->find($id);

        if (!$event) {
            return $this->json(
                ['message' => 'Événement non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Utilisation de la variable $entityManager injectée
        $entityManager->remove($event);
        $entityManager->flush();

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
