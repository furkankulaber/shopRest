<?php

namespace App\Exception;

use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class ApiException
{

    /** @var string */
    private string $errorCode;


    public function createException(string $code, array $replacements = [], string $message = null, int $httpStatusCode = null, Throwable $previous = null)
    {
        $this->errorCode = $code;
        if ($message === "" || $message === null) {
            $message = '';
        }
        $httpStatusCode = $httpStatusCode ?? $this->identifyHttpStatusCode($code);
        return new ApiCustomException($message, $httpStatusCode, $code);
    }

    private function identifyHttpStatusCode(string $code): int
    {
        $splice = explode('.', $code);
        return (int)(is_numeric($splice[0]) ? $splice[0] : $splice[1]);
    }

    public function getBCode()
    {
        return $this->errorCode;
    }
}
