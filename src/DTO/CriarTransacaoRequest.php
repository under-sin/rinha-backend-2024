<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\TipoTransacao;

final readonly class CriarTransacaoRequest
{
    // constructor property promotion
    public function __construct(
        #[Assert\Positive]
        public int $valor,

        public TipoTransacao $tipo,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 10)]
        public string $descricao,
    ) {}
}