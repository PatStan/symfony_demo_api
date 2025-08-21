<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\CreateEnquiryRequest;
use App\Service\EnquiryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

final class EnquiryController extends AbstractController
{
    public function __construct(
        private readonly EnquiryService $enquiryService,
    ) {}


    #[Route('/api/subscriber/{subscriberId}/enquiry', name: 'subscriber_enquiry_create', methods: ['POST'])]
    public function createSubscriberEnquiry(
        #[MapRequestPayload] CreateEnquiryRequest $request,
        string $subscriberId,
    ): JsonResponse
    {
        $data = $this->enquiryService->create($subscriberId, $request->message);

        return new JsonResponse([
            'enquiry' => $data['enquiry'],
            'subscriber' => $data['subscriber'],
        ]);
    }
}
