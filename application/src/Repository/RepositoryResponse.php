<?php


namespace App\Repository;

class RepositoryResponse
{
    /** @var bool  */
    private bool $success;

    /** @var string  */
    private string $message;

    /** @var mixed */
    private $response;

    /** @var \Exception|null  */
    private ?\Exception $exception = null;

    /** @var int  */
    private int $totalRow = 0;

    public function __construct($response = null, bool $success = true, string $message = "İşleminiz başarıyla gerçekleştirilmiştir.", ?\Exception $exception = null)
    {
        $this->setSuccess($success)->setMessage($message)->setResponse($response)->setException($exception);
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
     * @return RepositoryResponse
     */
    public function setSuccess(bool $success): RepositoryResponse
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
     * @return RepositoryResponse
     */
    public function setMessage(string $message): RepositoryResponse
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     * @return RepositoryResponse
     */
    public function setResponse($response)
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
     * @return RepositoryResponse
     */
    public function setException(?\Exception $exception): RepositoryResponse
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalRow(): int
    {
        return $this->totalRow;
    }

    /**
     * @param  int  $totalRow
     *
     * @return RepositoryResponse
     */
    public function setTotalRow(int $totalRow): RepositoryResponse
    {
        $this->totalRow = $totalRow;
        return $this;
    }

}
