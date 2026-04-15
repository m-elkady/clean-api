<?php

namespace App\Request;

use App\Exception\ValidationException;
use App\Response\AppResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    public function validate(ValidatorInterface $validator): void
    {
        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }


}