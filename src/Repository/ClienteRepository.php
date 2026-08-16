<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

final class ClienteRepository {

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    // php permite union types.
    public function buscarPorIdParaAtualizacao(int $clienteId): array|false {

        // fetchAssociative retorna um array associativo com os dados do cliente, ou false se não encontrar.
        return $this->connection->fetchAssociative(
            'SELECT * FROM clientes 
                WHERE id = :id
                FOR UPDATE',
            ['id' => $clienteId]
        );
    }

    public function atualizarSaldo(int $clienteId, int $novoSaldo): void {
        $this->connection->update(
            'clientes',
            ['saldo' => $novoSaldo],
            ['id' => $clienteId]
        );
    }
}