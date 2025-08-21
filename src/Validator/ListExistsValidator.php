<?php

declare(strict_types=1);

namespace App\Validator;

use App\Service\ListService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ListExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ListService $listProvider,
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ListExists) {
            throw new UnexpectedTypeException($constraint, ListExists::class);
        }

        $lists = $this->listProvider->getLists();

        $listIds = array_keys($lists);

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // Check if value is a match id in lists array
        if (!in_array($value, $listIds, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
