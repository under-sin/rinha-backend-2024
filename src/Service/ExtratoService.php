<?php 

namespace App\Service;
use App\Repository\TransacaoRepository;
use App\Exception\ClienteNaoEncontradoException;
use App\Repository\ClienteRepository;
use Doctrine\DBAL\Connection;

final class ExtratoService {
    public function __construct(
        private readonly Connection $connection,
        private readonly ClienteRepository $clienteRepository,
        private readonly TransacaoRepository $transacaoRepository
    ) {}

    public function buscar(int $clienteId): array {
        // para evitar inconsistências, vamos buscar o cliente e as transações dentro de uma transação do banco de dados.
        // isso seria um snapshot do estado do cliente e das transações no momento da consulta.
        // evitando que, por exemplo, o saldo seja alterado enquanto estamos buscando as transações.
        $this->connection->beginTransaction();

        try {
            $cliente = $this->clienteRepository->buscarPorId($clienteId);

            if ($cliente === false) {
                throw new ClienteNaoEncontradoException("Cliente não encontrado.");
            }

            $transacoes = $this->obterTransacoes($clienteId);
            $dataExtrato = new \DateTimeImmutable()->format(\DateTimeInterface::ATOM);

            $this->connection->commit();
            
            return [
                'saldo' => [
                    'limite' => (int) $cliente['limite'],
                    'saldo' => (int) $cliente['saldo'],
                    'data_extrato' => $dataExtrato,
                ],
                'ultimas_transacoes' => $transacoes
            ];
        } catch (\Throwable $th) {
            $this->connection->rollBack();
            throw $th;
        }
    }

    private function obterTransacoes(int $clienteId): array {
        $transacoes = $this->transacaoRepository->buscarUltimosPorClienteId($clienteId);
        
        $ultimasTransacoes = array_map(
            static fn (array $transacao): array => [
                'valor' => (int) $transacao['valor'],
                'tipo' => $transacao['tipo'],
                'descricao' => $transacao['descricao'],
                'realizada_em' => $transacao['realizada_em'],
            ],
            $transacoes
        );

        return $ultimasTransacoes;
    }
}