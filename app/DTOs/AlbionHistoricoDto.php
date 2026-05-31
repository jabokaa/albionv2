<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

final class AlbionHistoricoDto
{
    public function __construct(
        public readonly string  $itemId,
        public readonly string  $location,
        public readonly int     $quality,
        public readonly int     $precoMedio,
        public readonly int     $quantidadeMediaVendida,
        public readonly ?Carbon $dataAtualizacao,
    ) {}
}
