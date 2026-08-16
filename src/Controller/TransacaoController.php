<?php

namespace App\Controller;

use App\DTO\CriarTransacaoRequest;
use App\Service\TransacaoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class TransacaoController {

    public function __construct(
        private readonly TransacaoService $transacaoService
    ) {}

    #[Route('/clientes/{id}/transacoes', methods: ['POST'])]
    public function criar(
        int $id,
        #[MapRequestPayload]
        CriarTransacaoRequest $transacao
    ): JsonResponse {   

        $resultado = $this->transacaoService->executar($id, $transacao);

        return new JsonResponse($resultado, JsonResponse::HTTP_OK);
    }
}