<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
#[UniqueEntity(fields: ['emailAddress'], message: 'This email address is already registered.')]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ulid mirrors external API ids
    #[ORM\Column(type: 'ulid')]
    private ?Ulid $ulid = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank]
    #[Assert\LessThan('today', message: 'Date of birth must be in the past.')]
    #[Assert\LessThanOrEqual('today -18 years', message: 'You must be at least 18 years old.')]
    private ?\DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(nullable: true)]
    private ?bool $marketingConsent = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $emailAddress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUlid(): ?Ulid
    {
        return $this->ulid;
    }

    public function setUlid(Ulid $ulid): static
    {
        $this->ulid = $ulid;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(string|\DateTimeImmutable|null $dateOfBirth): static
    {
        if (is_string($dateOfBirth)) {
            try {
                $dateOfBirth = new \DateTimeImmutable($dateOfBirth);
            } catch (\Exception $e) {
                // Instead of throwing, you might want to set null or log
                $dateOfBirth = null;
            }
        }
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function isMarketingConsent(): ?bool
    {
        return $this->marketingConsent;
    }

    public function setMarketingConsent(?bool $marketingConsent): static
    {
        $this->marketingConsent = $marketingConsent;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): static
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }
}
