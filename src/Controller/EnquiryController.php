<?php

namespace App\Controller;

use App\Form\EnquiryType;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class EnquiryController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface    $serializer,
        private readonly EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
    ) {
    }


    #[Route('/api/subscribers/{subscriberId}/enquiries', name: 'subscriber_enquiry_create', methods: ['POST'])]
    public function createSubscriberEnquiry(string $subscriberId, Request $request, SubscriberRepository $subscriberRepository): JsonResponse
    {
        $form = $this->createForm(EnquiryType::class);
        $form->submit($request->toArray());

        // TODO: Check if the subscriberId is valid

        try {
            $ulid = new Ulid($subscriberId);
        } catch (\InvalidArgumentException) {
            return new JsonResponse(['error' => 'Invalid subscriber ID format'], 400);
        }

        // TODO: Check if the subscriber exists in the  database via ulid
        // skipped for now because of time constraint

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $payload = [
                'message' => $data['message'],
            ];

            $token = $this->getParameter('HTTP_TOKEN');

            if (!$token) {
                return new JsonResponse(['error' => 'Authorization token is missing'], 401);
            }

            $response = $this->httpClient->request('POST', 'https://devtest-crm-api.standard.aws.prop.cm/api/subscriber/' . $subscriberId . '/enquiry', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = $response->toArray();

            if ($response->getStatusCode() !== 200) {
                return new JsonResponse(['error' => 'Failed to create enquiry'], $response->getStatusCode());
            }

            return new JsonResponse([
                'message' => 'Enquiry created successfully',
                'enquiry' => $responseData['enquiry'],
            ], 201);
        }

        $errors = $this->serializer->normalize($form, 'json');

        return new JsonResponse([
            'errors' => $errors,
        ], 422);
    }
}
