<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class ValidationExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $validationException = $exception->getPrevious();

        if (!$validationException instanceof ValidationFailedException) {
            return;
        }

        $errors = [];

        foreach ($validationException->getViolations() as $violation) {
            $field = $violation->getPropertyPath() ?: 'request';

            $errors[$field][] = $violation->getMessage();
        }

        $event->setResponse(new JsonResponse(
            [
                'errors' => $errors
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
        ));
    }
}