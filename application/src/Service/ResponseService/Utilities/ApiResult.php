<?php


namespace App\Service\ResponseService\Utilities;

use App\Service\ResponseService\ResponseInterface;

class ApiResult implements ResponseInterface
{

    /** @var mixed */
    private $set;

    /**
     * ApiResult constructor.
     * @param mixed $result
     */
    public function __construct($result = null)
    {
        $this->set = $result;
    }

    /**
     * @return mixed
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * @param mixed $set
     * @return ApiResult
     */
    public function setSet($set): ApiResult
    {
        $this->set = $set;
        return $this;
    }

    public function outputToArray(): array
    {
        return array(
            'set' => $this->getSet()
        );
    }
}
