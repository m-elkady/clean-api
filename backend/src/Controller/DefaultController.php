<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{

    #[Route(path: '/', name: 'index', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['message' => 'Welcome to my assessment Task']);
    }

}