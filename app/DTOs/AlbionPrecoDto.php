<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

final class AlbionPrecoDto
{
    public function __construct(
        public readonly string  $itemId,
        public readonly string  $city,
        public readonly int     $quality,
        public readonly int     $valor,
        public readonly int     $ordemDeCompra,
        public readonly ?Carbon $dataAtualizacao,
    ) {}
}
