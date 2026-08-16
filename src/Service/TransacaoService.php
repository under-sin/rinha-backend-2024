<?php

namespace App\Service;
    
use App\DTO\CriarTransacaoRequest;
use App\Enum\TipoTransacao;
use App\Exception\ClienteNaoEncontradoException;
use App\Exception\LimiteInsuficienteException;
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
            
            $cliente = $this->executarTransacao($clienteId, $transacao);

            if ($cliente === false) {
                $this->tratarFalha($clienteId);
            }

            $this->transacaoRepository->insert(
                $clienteId,
                $transacao->valor,
                $transacao->tipo->value,
                $transacao->descricao
            );

            $this->connection->commit();

            return [
                'limite' => $cliente['limite'],
                'saldo' => $cliente['saldo'],
            ];
        } catch (\Throwable $th) {
            
            $this->connection->rollBack();
            throw $th;
        }
    }

    private function executarTransacao(
        int $clienteId, 
        CriarTransacaoRequest $transacao
    ): array|false {
        $resultado = match ($transacao->tipo) {
            TipoTransacao::CREDITO => $this->clienteRepository->creditar($clienteId, $transacao->valor),
            TipoTransacao::DEBITO => $this->clienteRepository->debitar($clienteId, $transacao->valor),
        };

        return $resultado;
    }

    private function tratarFalha(int $clienteId): never {
        $cliente = $this->clienteRepository->buscarPorId($clienteId);

        if ($cliente === false) {
            throw new ClienteNaoEncontradoException("Cliente não encontrado.");
        }

        error_log("Excedeu o limite do cliente {$clienteId}. Saldo: {$cliente['saldo']}, Limite: {$cliente['limite']}");

        // daria para colocar um redis ou outro mecanismo de cache para bloquear o cliente por um tempo, evitando que ele tente novamente e continue a gerar exceções quando o limite estiver excedido. 
        throw new LimiteInsuficienteException("Transação não permitida. Limite excedido.");
    }
}