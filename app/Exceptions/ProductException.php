<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Throwable;

class ProductException extends Exception
{
    public function __construct(
        string $message = "",
        int $code = Response::HTTP_PARTIAL_CONTENT,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
