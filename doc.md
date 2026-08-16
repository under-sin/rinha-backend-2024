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

No momento não vou usar ela porque quero entender mais sobre o locking do postgres e como ele funciona com concorrência, e também quero entender melhor sobre transações concorrentes no doctrine.