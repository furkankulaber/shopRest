<?php


namespace App\Listener;

use App\Exception\ApiCustomException;
use App\Exception\ApiException;
use App\Service\ResponseService\Constants;
use App\Service\ResponseService\Service as ResponseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Exception\ValidatorException;

class ExceptionListener
{
    /** @var ResponseService */
    protected ResponseService $responseService;

    /** @var ApiException */
    private ApiException $apiException;

    public function __construct(ResponseService $responseService, ApiException $apiException)
    {
        $this->responseService = $responseService;
        $this->apiException = $apiException;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $data = array(
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        );

        if ($exception instanceof NotFoundHttpException) {
            $exception = $this->getApiException()->createException(Constants::MSG_404_0000);
        } else {
            if ($exception instanceof ValidatorException) {
                $exception = $this->getApiException()->createException(Constants::MSG_412_9999,
                    ['invalidPropertyName' => $exception->getMessage()]);
            } else {
                if ($exception instanceof ApiCustomException) {
                    $exception = $this->getApiException()->createException($exception->getInternalCode(),
                        $exception->getReplacements(), $exception->getMessage());
                } else {
                    if (!$exception instanceof ApiException) {
                        $httpStatusCode = $exception instanceof AuthenticationCredentialsNotFoundException ? Constants::MSG_500_0000 :
                            ($exception instanceof AccessDeniedException ? Constants::MSG_401_9000 : $this->getCode($exception->getCode()));
                        $exception = $this->getApiException()->createException($httpStatusCode);
                    }
                }
            }
        }

        $code = $this->getApiException()->getBCode();
        $message = $exception->getMessage();

        $response = $this->responseService->withException($exception)->toJsonResponse(null, $code, $message);

        $event->setResponse($response);
    }

    private function getCode($httpStatusCode)
    {
        switch ((int)$httpStatusCode) {
            case 0:
                $code = '500.0000';
                break;

            default:
                $code = (string)$httpStatusCode.".0000";
        }

        return "msg.{$code}";
    }

    /**
     * @return ResponseService
     */
    public function getResponseService(): ResponseService
    {
        return $this->responseService;
    }

    /**
     * @return ApiException
     */
    public function getApiException(): ApiException
    {
        return $this->apiException;
    }
}
