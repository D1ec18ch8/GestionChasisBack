<?php

namespace App\Exceptions;

use Exception;

class UbicacionNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("No se encontro la ubicacion con ID {$id}.");
    }
}
