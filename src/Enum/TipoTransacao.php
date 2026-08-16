<?php

namespace App\Enum;

enum TipoTransacao: string
{
    case CREDITO = 'c';
    case DEBITO = 'd';
}