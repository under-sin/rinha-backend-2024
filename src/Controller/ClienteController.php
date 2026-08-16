<?php

namespace App\Controller;

use App\DTO\CriarTransacaoRequest;
use App\Service\ExtratoService;
use App\Service\TransacaoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class ClienteController {

    public function __construct(
        private readonly TransacaoService $transacaoService,
        private readonly ExtratoService $extratoService
    ) {}

    #[Route('/clientes/{id}/transacoes', methods: ['POST'])]
    public function criar(
        int $id,
        #[MapRequestPayload]
        CriarTransacaoRequest $transacao
    ): JsonResponse {   

        $resultado = $this->transacaoService->executar(
            clienteId: $id, 
            transacao: $transacao
        );

        return new JsonResponse($resultado, JsonResponse::HTTP_OK);
    }

    #[Route('/clientes/{id}/extrato', methods: ['GET'])]
    public function extrato(int $id): JsonResponse {

        $resultado = $this->extratoService->buscar(clienteId: $id);

        return new JsonResponse($resultado, JsonResponse::HTTP_OK);
    }
}