<?php


namespace App\Service\ResponseService;

use App\Service\ResponseService\Utilities\ApiResponse;
use App\Service\ResponseService\Utilities\ApiResult;
use App\Traits\Serialize;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class Service
{
    use Serialize;

    /** @var ContainerInterface  */
    private ContainerInterface $container;

    /** @var Security  */
    private Security $security;

    /** @var null|\Exception  */
    private ?\Exception $exception = null;

    /** @var string|null */
    private ?string $sessionToken = null;

    public function __construct(ContainerInterface $container, Security $security)
    {
        $this->container = $container;
        $this->security = $security;
    }
    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    private function identifyCode(string $code): array
    {
        list($msgIdentifier, $statusCode, $statusSubCode) = explode('.', $code);

        return array(
            'httpStatusCode' => (int) $statusCode,
            'code' => implode('.', [$statusCode, $statusSubCode])
        );
    }

    public function withSessionToken(string $sessionToken)
    {
        $this->sessionToken = $sessionToken;
        return $this;
    }

    public function withException(\Exception $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @param ResponseInterface|array|int|string|null|object $result
     * @param string|null $code
     * @param string|null $message
     * @param array $replacements
     * @return JsonResponse
     */
    public function toJsonResponse(
        $result = null,
        ?string $code = null,
        ?string $message = null,
        array $replacements = []
    ): JsonResponse
    {
        $code = $code ?? Constants::MSG_200_0000;
        $message = $message ?? 'İşleminiz başarıyla gerçekleştirildi';

        $identifiedStatusCode = $this->identifyCode($code);

        $sessionToken = $this->sessionToken;
        $apiResult = new ApiResult($this->prepareDataForJsonResponse($result));
        $apiResponse = new ApiResponse($identifiedStatusCode['code'], $message, $apiResult, $sessionToken);
        $response = $apiResponse->outputToArray();

        if ('dev' == $_ENV['APP_ENV'] && $this->exception instanceof \Exception) {
            $response['exception'] = $this->exception->getTraceAsString();
        }

        return new JsonResponse($response, $identifiedStatusCode['httpStatusCode']);
    }

    private function getSessionTokenFromStorage(): ?string
    {
        $token = null;
        if (
            null !== $this->security->getToken() &&
            $this->security->getToken()->getUser() instanceof Authenticated &&
            $this->security->getToken()->getUser()->getSession() instanceof PlayerSession
        ) {
            $token = $this->security->getToken()->getUser()->getSession()->getToken();
        }

        return $token;
    }



    private function prepareDataForJsonResponse($data,$recursive=false)
    {
        if ($data instanceof ResponseInterface) {
            return $data->outputToArray();
        }
        if(is_object($data)){
            return json_decode($this->getSerializer()->serialize($data,'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]));
        }

        if (is_array($data)) {
            $response = [];
            foreach ($data as $k => $v) {
                $response[$k] = $this->prepareDataForJsonResponse($v,true);
            }

            return $response;
        }
        if($recursive === true ){
            return $data;
        }
        if($data === null){
            return $data;
        }
        return ['value' => $data];
    }
}
