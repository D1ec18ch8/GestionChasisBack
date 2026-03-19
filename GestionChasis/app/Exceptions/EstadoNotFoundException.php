<?php

namespace App\Exceptions;

use Exception;

class EstadoNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("No se encontro el estado con ID {$id}.");
    }
}
