<?php

namespace App\Resolver;

use App\Request\BaseRequest;
use App\Service\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly Serializer $serializer
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if (!$type || !is_subclass_of($type, BaseRequest::class)) {
            return [];
        }

        $data = match ($request->getMethod()) {
            'GET', 'DELETE' => json_encode($request->query->all()),
            default => $request->getContent() ?? [],
            };
            
        
        $requestType = $this->serializer->deserialize($data, $type, 'json');
        
        $requestType->validate($this->validator);

        yield $requestType;
    }
}
