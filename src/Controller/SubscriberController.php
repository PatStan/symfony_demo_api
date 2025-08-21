<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Subscriber;
use App\Form\SubscriberType;
use App\Request\CreateOrUpdateSubscriberRequest;
use App\Service\ListService;
use App\Service\SubscriberService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SubscriberController extends AbstractController
{
    public function __construct(
        private readonly SubscriberService $subscriberService,
    ) {
    }

    #[Route('/api/subscriber', name: 'create_or_update_subscriber', methods: ['PUT'])]
    public function createOrUpdate(
        #[MapRequestPayload] CreateOrUpdateSubscriberRequest $request,
    ): JsonResponse
    {
        $data = $this->subscriberService->subscribe(
            $request->emailAddress,
            $request->firstName,
            $request->lastName,
            $request->dateOfBirth,
            $request->marketingConsent,
            $request->lists,
        );

        return new JsonResponse([
            'message' => 'Subscriber created or updated successfully.',
            'subscriber' => $data['subscriber'],
        ]);
    }
}
