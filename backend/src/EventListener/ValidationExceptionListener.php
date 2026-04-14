<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationException) {
            return;
        }

        $event->setResponse($exception->getResponse());
        $event->stopPropagation();
    }
}
