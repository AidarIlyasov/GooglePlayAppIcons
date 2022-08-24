<?php

namespace App\Exceptions;

use Exception;

class BadAppNameException extends Exception
{
    public function render($request)
    {
        return response('Wrong application name was passed', 500);
    }
}
