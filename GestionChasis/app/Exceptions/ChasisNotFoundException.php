<?php

namespace App\Exceptions;

use Exception;

class ChasisNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("No se encontro el chasis con ID {$id}.");
    }
}
