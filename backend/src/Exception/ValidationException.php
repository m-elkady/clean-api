<?php

namespace App\Exception;

use App\Response\AppResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationException extends HttpException
{
    public function __construct(
        private readonly ConstraintViolationList $violations,
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY
    ) {
        parent::__construct($code, 'Validation failed');
    }

    public function getViolations(): ConstraintViolationList
    {
        return $this->violations;
    }

    public function getErrorsArray(): array
    {
        $errors = [];
        foreach ($this->violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $errors;
    }

    public function getResponse(): JsonResponse
    {
       return AppResponse::validationError($this->getErrorsArray());
    }
}
