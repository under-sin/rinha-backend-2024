<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

final class ClienteRepository {

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    // php permite union types.
    public function buscarPorId(int $clienteId): array|false {

        // fetchAssociative retorna um array associativo com os dados do cliente, ou false se não encontrar.
        return $this->connection->fetchAssociative(
            'SELECT * FROM clientes WHERE id = :id',
            ['id' => $clienteId]
        );
    }

    public function debitar(int $clienteId, int $valor): array|false {
        return $this->connection->fetchAssociative(
            '
                UPDATE clientes 
                SET saldo = saldo - :valor
                WHERE id = :id
                    AND (saldo - :valor) >= -limite
                RETURNING saldo, limite
            ',
            [
                'id' => $clienteId,
                'valor' => $valor,
            ]
        );
    }

    public function creditar(int $clienteId, int $valor): array|false {
        return $this->connection->fetchAssociative(
            '
                UPDATE clientes 
                SET saldo = saldo + :valor
                WHERE id = :id
                RETURNING saldo, limite
            ',
            [
                'id' => $clienteId,
                'valor' => $valor,
            ]
        );
    }
}