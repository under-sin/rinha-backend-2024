`composer require symfony/framework-bundle` pacote necessário para instalar o Symfony Framework Bundle, que é um conjunto de componentes essenciais para criar aplicações web com o Symfony. Ele fornece funcionalidades como roteamento, controle de dependências, gerenciamento de serviços e muito mais. Ao executar esse comando, o Composer irá baixar e instalar o pacote junto com suas dependências, permitindo que você utilize os recursos do Symfony em seu projeto.


`composer require symfony/validator symfony/serializer-pack` pacotes para validação e serialização de dados.

autowiring no symfony: "Symfony, encontre minhas classes dentro de src/
e tente resolver suas dependências automaticamente."

`composer require doctrine/doctrine-bundle doctrine/dbal` pacotes para integração com o banco de dados usando Doctrine.

"Para construir TransacaoController,
preciso de TransacaoService."

Container
   │y
   ├─ cria TransacaoService
   │
   ▼
cria TransacaoController(
    TransacaoService
)

Doctrine DBAL = Dapper do .NET
```php
$cliente = $connection->fetchAssociative(
    'SELECT * FROM clientes WHERE id = ?',
    [$id],
);
```


provavelmente a estrategia mais performatica para a rinha seja:
BEGIN
↓
UPDATE atômico + condição
↓
RETURNING
↓
INSERT transação
↓
COMMIT

Toda a logica de validação de saldo e limite pode ser feita no UPDATE atômico, evitando race conditions

No momento não vou usar ela porque quero entender mais sobre o locking do postgres e como ele funciona com concorrência.


`RETURNING` é uma cláusula SQL usada em bancos de dados relacionais, como PostgreSQL, que permite retornar valores de colunas específicas após a execução de uma operação de modificação de dados, como INSERT, UPDATE ou DELETE. Caso nenhuma linha seja afetada, o retorno será nulo. Isso é útil para obter informações sobre os registros modificados sem a necessidade de executar uma consulta separada.

`hey` é uma ferramenta de linha de comando para realizar testes de carga em APIs HTTP. Ela permite enviar múltiplas requisições simultâneas para um endpoint específico, ajudando a avaliar o desempenho e a capacidade de resposta do serviço sob diferentes condições de carga.

-n 1000: total de requisições.
-c 50: até 50 requisições concorrentes

```bash
hey -n 1000 -c 50 \
  -m POST \
  -H 'Content-Type: application/json' \
  -d '{"valor":5000,"tipo":"d","descricao":"teste"}' \
  http://127.0.0.1:8000/clientes/2/transacoes
```

3200 req/s
↓
"minha capacidade observada nesse teste"

p50 10ms
↓
"metade terminou em até 10ms"

p95 60ms
↓
"95 de cada 100 terminaram em até 60ms"

p99 250ms
↓
"99 de cada 100 terminaram em até 250ms;
a cauda está bem mais lenta"