<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(
        public readonly int $productoId,
        public readonly ?int $varianteId,
        public readonly int $stockActual,
        public readonly int $cantidadSolicitada,
        ?string $message = null,
    ) {
        parent::__construct(
            $message ?? sprintf(
                'Stock insuficiente para producto %d (variante %s): %d disponible, %d solicitado.',
                $productoId,
                $varianteId ?? 'sin variante',
                $stockActual,
                $cantidadSolicitada,
            ),
            422,
        );
    }
}
