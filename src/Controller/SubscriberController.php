<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Form\SubscriberType;
use App\Service\ListProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SubscriberController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface    $serializer,
        private readonly EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
    ) {
    }

    #[Route('/api/subscribers', name: 'create_subscriber', methods: ['POST'])]
    public function create(Request $request, ListProvider $listProvider): JsonResponse
    {
        $subscriber = new Subscriber();
        $lists = $listProvider->getLists();
        $form = $this->createForm(SubscriberType::class, $subscriber, [
            'lists' => $lists,
        ]);
        $data = $request->toArray();
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            // If marketing consent is missing, set it to false

            if (!isset($data['marketingConsent'])) {
                $data['marketingConsent'] = false;
            }

            // if marketing consent is true, add lists to the payload
            // otherwise, set lists to an empty array

            $payload = $data;

            if (!$subscriber->isMarketingConsent()) {
                $payload['lists'] = [];
            }

            $token = $this->getParameter('HTTP_TOKEN');

            if (!$token) {
                return new JsonResponse(['error' => 'Authorization token is missing'], 401);
            }

            $response = $this->httpClient->request('POST', 'https://devtest-crm-api.standard.aws.prop.cm/api/subscriber', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getParameter('HTTP_TOKEN'),
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

            if ($response->getStatusCode() !== 200) {
                return new JsonResponse(['error' => 'Failed to create subscriber'], $response->getStatusCode());
            }

            // set the ULID from the response
            $responseData = $response->toArray();
            $subscriber->setUlid(new Ulid($responseData['subscriber']['id']));

            $this->entityManager->persist($subscriber);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Subscriber created successfully',
                'subscriber' => $this->serializer->normalize($subscriber, 'json'),
            ], 201);
        }

        // If the form is not valid, return the errors
        $errors = $this->serializer->normalize($form, 'json');

        return new JsonResponse([
            'errors' => $errors,
        ], 422);
    }
}
