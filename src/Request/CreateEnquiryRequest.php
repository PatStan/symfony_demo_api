<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateEnquiryRequest
{
    #[Assert\NotBlank]
    public string $message;
}
