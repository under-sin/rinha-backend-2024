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
}