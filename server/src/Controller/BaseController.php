<?php

namespace App\Controller;

use App\Request\AbstractRequest;
use App\Response\BaseResponse;
use App\Service\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class BaseController extends AbstractController
{
    protected readonly Serializer $serializer;

    #[Required]
    function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getRequest(mixed $data, string $requestClass, $format = 'json')
    {
        return $this->serializer->deserialize($data, $requestClass, $format);
    }

    public function getResponse($response, $format = 'json'): Response
    {
        $actionResponse  = $this->serializer->serialize($response, $format);

        return new JsonResponse($actionResponse, $response->code, json: true);

    }
}
