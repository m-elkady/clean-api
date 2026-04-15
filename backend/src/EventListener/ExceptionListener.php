<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use App\Response\AppResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Custom ValidationException
        if ($exception instanceof ValidationException) {
            $event->setResponse($exception->getResponse());
            $event->stopPropagation();
            return;
        }

        // Symfony HttpException handling
        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(AppResponse::notFound($exception->getMessage() ?: 'Resource not found'));
            $event->stopPropagation();
            return;
        }

        if ($exception instanceof UnauthorizedHttpException) {
            $event->setResponse(AppResponse::unauthorized($exception->getMessage() ?: 'Access denied'));
            $event->stopPropagation();
            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse(AppResponse::error(
                $exception->getMessage() ?: 'HTTP error',
                $exception->getStatusCode()
            ));
            $event->stopPropagation();
            return;
        }

        // Generic fallback - log the exception for debugging
        $event->setResponse(AppResponse::error(
            $exception->getMessage(),
            500
        ));
    }
}