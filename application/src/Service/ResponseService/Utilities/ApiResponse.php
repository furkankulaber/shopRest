<?php


namespace App\Service\ResponseService\Utilities;


use App\Service\ResponseService\ResponseInterface;

class ApiResponse implements ResponseInterface
{

    /** @var string  */
    private string $code;

    /** @var string  */
    private string $message;

    /** @var string|null  */
    private ?string $sessionToken = null;

    /** @var ApiResult  */
    private ApiResult $result;

    /**
     * ApiResponse constructor.
     *
     * @param string $code
     * @param string $message
     * @param string|null $sessionToken
     * @param ApiResult $result
     */
    public function __construct(string $code, string $message, ApiResult $result, ?string $sessionToken = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->sessionToken = $sessionToken;
        $this->result = $result;
    }


    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return ApiResponse
     */
    public function setCode(string $code): ApiResponse
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ApiResponse
     */
    public function setMessage(string $message): ApiResponse
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSessionToken(): ?string
    {
        return $this->sessionToken;
    }

    /**
     * @param string|null $sessionToken
     * @return ApiResponse
     */
    public function setSessionToken(?string $sessionToken): ApiResponse
    {
        $this->sessionToken = $sessionToken;
        return $this;
    }

    /**
     * @return ApiResult
     */
    public function getResult(): ApiResult
    {
        return $this->result;
    }

    /**
     * @param ApiResult $result
     * @return ApiResponse
     */
    public function setResult(ApiResult $result): ApiResponse
    {
        $this->result = $result;
        return $this;
    }

    public function outputToArray(): array
    {
        return array(
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'sessionToken' => $this->getSessionToken(),
            'result' => $this->getResult()->outputToArray()
        );
    }
}
