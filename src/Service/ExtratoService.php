<?php 

namespace App\Service;
use App\Repository\TransacaoRepository;
use App\Exception\ClienteNaoEncontradoException;
use App\Repository\ClienteRepository;

final class ExtratoService {
    public function __construct(
        private readonly ClienteRepository $clienteRepository,
        private readonly TransacaoRepository $transacaoRepository
    ) {}

    public function buscar(int $clienteId): array {
        $cliente = $this->clienteRepository->buscarPorId($clienteId);

        if ($cliente === false) {
            throw new ClienteNaoEncontradoException("Cliente não encontrado.");
        }

        $transacoes = $this->obterTransacoes($clienteId);
        $dataExtrato = new \DateTimeImmutable()->format(\DateTimeInterface::ATOM);

        return [
            'saldo' => [
                'limite' => (int) $cliente['limite'],
                'saldo' => (int) $cliente['saldo'],
                'data_extrato' => $dataExtrato,
            ],
            'ultimas_transacoes' => $transacoes
        ];
    }

    private function obterTransacoes(int $clienteId): array {
        $transacoes = $this->transacaoRepository->buscarUltimosPorClienteId($clienteId);
        
        $ultimasTransacoes = array_map(
            static fn (array $transacao): array => [
                'valor' => (int) $transacao['valor'],
                'tipo' => $transacao['tipo'],
                'descricao' => $transacao['descricao'],
                'realizado_em' => $transacao['realizado_em'],
            ],
            $transacoes
        );

        return $ultimasTransacoes;
    }
}