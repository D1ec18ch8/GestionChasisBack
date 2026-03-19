<?php

namespace App\Exceptions;

use Exception;

class ProtectedEstadoException extends Exception
{
    public function __construct(string $slug)
    {
        parent::__construct("El estado base '{$slug}' no puede modificarse ni eliminarse.");
    }
}
