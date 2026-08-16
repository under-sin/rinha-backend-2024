Rinha 2024

### Transações

Response da transação
```json
{
    "limite" : 100000,
    "saldo" : -9098
}
```

- Uma transação de DÉBITO nunca pode deixar o saldo menor do que seu LIMITE disponível.

- Retornar 422 para as validações de descrição, tipo transação e valor.

- Retornar 404 caso o [id] da url for um identificador não existente de cliente.

- As transações vão definir o saldo do cliente, e o saldo do cliente não pode ser menor que o limite.
- O saldo do cliente começa em 0 e o limite em X e não pode ser alterado.

O limite aqui não significa "saldo máximo da conta". Ele representa quanto o cliente pode ficar negativo.
-999 >= -1000 e -1001 < -1000
saldo não pode atravessar -limite

Logica do debito:
- novoSaldo = saldoAtual - valorTransacao
novoSaldo >= -limite

Logica do credito:
- novoSaldo = saldoAtual + valorTransacao
- Não existe verificação de limite para transações de crédito, pois o saldo do cliente pode ser positivo.


### Tabelas

*Clientes*

ID     | int          | primary key
Nome   | varchar(100) | required
Limite | bigint       | required
Saldo  | bigint       | required

*Transacoes*

ID          | int         | primary key
ClienteID   | int         | foreign key (Clientes.ID)
Valor       | bigint      | required
Descricao   | varchar(10) | required
Tipo        | char(1)     | required
RealizadoEm | timestamp   | required

Um cliente pode ter várias transações, mas uma transação pertence a apenas um cliente.


## Ordem de versões

[] API Funcionar
[] Postgres + Transações concorrentes
[] Concorrência correta
[] 2 Instâncias + load balancer
[] Limites oficiais de CPU/Memória
[] Redis?
[] Gatling
[] Profiling
[] Otimizações