<?php

namespace App\Exceptions;

use Exception;

class EstadoInUseException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("No se puede eliminar el estado con ID {$id} porque esta en uso por uno o mas chasis.");
    }
}
