<?php

namespace App\EventListener;

use App\Exception\ClienteNaoEncontradoException;
use App\Exception\LimiteInsuficienteException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class TransacaoExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = match (true) {
            $exception instanceof ClienteNaoEncontradoException 
                => JsonResponse::HTTP_NOT_FOUND,
                
            $exception instanceof LimiteInsuficienteException 
                => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            default => null,
        };

        if ($statusCode === null) {
            return;
        }

        $event->setResponse(new JsonResponse(
            [
                'errors' => ['request' => [$exception->getMessage()]]
            ],
            $statusCode,
        ));
    }
}