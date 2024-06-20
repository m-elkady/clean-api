<?php

namespace App\Request;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class BaseRequest
{
    public function getValidationErrors(ConstraintViolationList $errors): array
    {
        $errs = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $errs[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errs;
    }
    public function getAttributes(): array
    {
        return get_object_vars($this);
    }

    public function getAttributesValues(): array
    {
        return array_filter($this->getAttributes(), fn($value) => !empty($value));
    }

}