<?php
// src/Controller/ApiController.php
namespace App\Controller;

use App\Repository\ContactSubmissionRepository;
use App\Entity\ContactSubmission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    #[Route('/api/contact', name: 'api_contact_list', methods: ['GET'])]
    public function listContacts(ContactSubmissionRepository $repo): JsonResponse
    {
        $subs = $repo->findBy([], ['submittedAt'=>'DESC']);
        $data = array_map(fn(ContactSubmission $c) => [
            'id'          => $c->getId(),
            'name'        => $c->getName(),
            'email'       => $c->getEmail(),
            'message'     => $c->getMessage(),
            'submittedAt' => $c->getSubmittedAt()->format('Y-m-d H:i:s'),
        ], $subs);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/api/contact', name: 'api_contact_submit', methods: ['POST'])]
    public function submitContact(
        Request $request,
        EntityManagerInterface $em,
        Validator\ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        // Ici, on mappe sur l’entité et on valide
        $contact = new ContactSubmission();
        $contact->setName($data['name'] ?? '');
        $contact->setEmail($data['email'] ?? '');
        $contact->setMessage($data['message'] ?? '');

        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            $errs = [];
            foreach ($errors as $e) {
                $errs[$e->getPropertyPath()][] = $e->getMessage();
            }
            return new JsonResponse(['errors'=>$errs], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($contact);
        $em->flush();
        return new JsonResponse(['message'=>'Enregistré'], JsonResponse::HTTP_CREATED);
    }
}