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

`docker stats` para ver o consumo de CPU e memória dos containers em tempo real.
NAME          CPU %     MEM USAGE / LIMIT

api1          87%       78MB / 100MB
api2          91%       81MB / 100MB
postgres      65%       240MB / 320MB
nginx          3%       8MB / 30MB

Quando os recursos chegam aperto de 100% as requests começam a esperar, com isso as filas aumentam, latência aumenta, p95 e p99 aumentam, e o throughput cai.

Subir a primeira versão para ter a baseline de performance, e depois ir fazendo otimizações e comparando com a baseline.

### Teste de concorrência com 10, 50 e 100 requisições concorrentes, medindo o throughput (req/s), latência p95 e p99, e consumo de CPU da API e do banco de dados.

| Concorrência | req/s | p95 | p99 | CPU API | CPU DB |
| -----------: | ----: | --: | --: | ------: | -----: |
|           10 |       |     |     |         |        |
|           50 |       |     |     |         |        |
|          100 |       |     |     |         |        |


### FrankenPHP
FrankenPHP é um servidor web que combina o Caddy e o PHP-FPM em um único processo. Ele é projetado para simplificar a implantação de aplicativos PHP, oferecendo uma configuração mais fácil e melhor desempenho em comparação com a configuração tradicional de Caddy + PHP-FPM.