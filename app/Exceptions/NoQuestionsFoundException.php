<?php

namespace App\Exceptions;

use Exception;

class NoQuestionsFoundException extends Exception
{

    protected $code = 500;

    public function __construct($message = "There is no questions found", $code = 500)
    {
        parent::__construct($message, $code);
    }

}
