<?php

namespace App\Core;

use Exception;

class ValidationException extends Exception
{
    public function __construct($message = "Erro de validação", $code = 400)
    {
        parent::__construct($message, $code);
    }
}
