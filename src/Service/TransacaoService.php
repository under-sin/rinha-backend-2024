<?php

namespace App\Service;
    
use App\DTO\CriarTransacaoRequest;
use App\Enum\TipoTransacao;
use App\Repository\ClienteRepository;
use App\Repository\TransacaoRepository;
use Doctrine\DBAL\Connection;

final class TransacaoService {

    public function __construct(
        private readonly Connection $connection,
        private readonly TransacaoRepository $transacaoRepository,
        private readonly ClienteRepository $clienteRepository
    ) {
    }

    public function executar(int $clienteId, CriarTransacaoRequest $transacao): array {
        // Usando o FOR UPDATE para bloquear a linha do cliente durante a transação, evitando race condition.
        $this->connection->beginTransaction();

        try {
            $cliente = $this->clienteRepository->buscarPorIdParaAtualizacao($clienteId);

            if ($cliente === false) {
                throw new \Exception('Cliente não encontrado');
            }

            $novoSaldo = $this->calculaSaldo(
                (int) $cliente['saldo'],
                $transacao->valor,
                $transacao->tipo
            );

            if ($novoSaldo < -$cliente['limite']) {
                throw new \Exception('Limite insuficiente');
            }

            $this->clienteRepository->atualizarSaldo(
                $clienteId, 
                $novoSaldo
            );

            $this->transacaoRepository->insert(
                $clienteId,
                $transacao->valor,
                $transacao->tipo->value,
                $transacao->descricao
            );

            $this->connection->commit();

            return [
                'limite' => $cliente['limite'],
                'saldo' => $novoSaldo,
            ];
        } catch (\Throwable $th) {
            
            $this->connection->rollBack();
            throw $th;
        }
    }

    private function calculaSaldo(
        int $saldoAtual, 
        int $valorTransacao, 
        TipoTransacao $tipoTransacao
    ): int {
        if ($tipoTransacao === TipoTransacao::CREDITO) {
            return $saldoAtual + $valorTransacao;
        }

        return $saldoAtual - $valorTransacao;
    }
}