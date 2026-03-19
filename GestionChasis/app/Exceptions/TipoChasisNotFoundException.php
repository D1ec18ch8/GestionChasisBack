<?php

namespace App\Exceptions;

use Exception;

class TipoChasisNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("No se encontro el tipo de chasis con ID {$id}.");
    }
}
