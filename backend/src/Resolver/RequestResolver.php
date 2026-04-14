<?php

namespace App\Resolver;

use App\Exception\ValidationException;
use App\Request\RequestValidatedInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if (!$type || !is_a($type, RequestValidatedInterface::class, true)) {
            return [];
        }

        $data = match ($request->getMethod()) {
            'GET', 'DELETE' => $request->query->all(),
            default => json_decode($request->getContent(), true) ?? [],
        };

        $requestData = $type::fromArray($data);

        // Validate the DTO
        $violations = $this->validator->validate($requestData);

        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        yield $requestData;
    }
}
