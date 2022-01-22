<?php


namespace App\Service;


class ServiceResponse
{
    /** @var bool  */
    private bool $success;

    /** @var string  */
    private string $message;

    private $response;

    /** @var string|null */
    private ?string $code = null;

    /** @var \Exception|null  */
    private ?\Exception $exception = null;

    public function __construct($response = null, bool $success = true, string $message = "İşleminiz başarıyla gerçekleştirilmiştir.")
    {
        if ($response instanceof \Exception) {
            $this->setSuccess(false)->setMessage($response->getMessage())->setResponse(null)->setException($response);
        }else{
            $this->setSuccess($success)->setMessage($message)->setResponse($response);
        }
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     * @return ServiceResponse
     */
    public function setSuccess(bool $success): ServiceResponse
    {
        $this->success = $success;
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
     * @return ServiceResponse
     */
    public function setMessage(string $message): ServiceResponse
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return ?array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return ServiceResponse
     */
    public function setResponse( $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return \Exception|null
     */
    public function getException(): ?\Exception
    {
        return $this->exception;
    }

    /**
     * @param \Exception|null $exception
     * @return ServiceResponse
     */
    public function setException(?\Exception $exception): ServiceResponse
    {
        $this->exception = $exception;
        return $this;
    }


    public function withCode($code): ServiceResponse
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }
}
