<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

final class TransacaoRepository {
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function insert(
        int $clientId,
        int $valor,
        string $tipo,
        string $descricao
    ): void {
        $this->connection->insert(
            'transacoes',
            [
                'cliente_id' => $clientId,
                'valor' => $valor,
                'tipo' => $tipo,
                'descricao' => $descricao,
            ]
        );
    }

    public function buscarUltimosPorClienteId(int $clienteId): array {
        return $this->connection->fetchAllAssociative(
            'SELECT 
                valor,
                tipo,
                descricao,
                realizada_em
             FROM transacoes
                WHERE cliente_id = :cliente_id 
                ORDER BY realizada_em DESC, id DESC
                LIMIT 10',
            [
                'cliente_id' => $clienteId
            ]
        );
    }
}