<?php

declare(strict_types=1);

namespace App\Validator;


use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ListExists extends Constraint
{
    public string $message = 'The list "{{ listId }}" does not exist.';

    public function __construct(?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
