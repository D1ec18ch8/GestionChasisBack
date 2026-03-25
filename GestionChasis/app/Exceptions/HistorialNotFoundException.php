<?php

namespace App\Exceptions;

use Exception;

class HistorialNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("No se encontro el historial con ID {$id}.");
    }
}
