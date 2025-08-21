<?php

declare(strict_types=1);

namespace App\Request;

use App\Validator\ListExists;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrUpdateSubscriberRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $emailAddress;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $firstName = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $lastName = null;

    #[Assert\Date]
    #[Assert\NotBlank]
    #[Assert\LessThan('today', message: 'Date of birth must be in the past.')]
    #[Assert\LessThanOrEqual('today -18 years', message: 'You must be at least 18 years old.')]
    public string $dateOfBirth;

    public ?bool $marketingConsent = null;

    #[Assert\All(
        [
            new Assert\NotBlank(),
            new ListExists(),
        ]
    )]
    public ?array $lists = null;
}
