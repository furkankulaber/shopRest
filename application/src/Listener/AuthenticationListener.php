<?php

namespace App\Listener;

use Container4xtoaoi\getResponseService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AuthenticationListener implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $statusCode = self::mapExceptionCodeToStatusCode($exception->getCode());

        $data = [
            'code' => $statusCode.'.0000',
            'message' => 'Geçersiz Kullanıcı adı veya Şifre',
            'result' => null
        ];

        $event = new AuthenticationFailureEvent(
            $exception,
            new JsonResponse($data, $statusCode)
        );

        $this->dispatcher->dispatch($event, Events::AUTHENTICATION_FAILURE);

        return $event->getResponse();
    }

    /**
     * @param string|int $exceptionCode
     */
    private static function mapExceptionCodeToStatusCode($exceptionCode): int
    {
        $canMapToStatusCode = is_int($exceptionCode)
            && $exceptionCode >= 400
            && $exceptionCode < 500;

        return $canMapToStatusCode
            ? $exceptionCode
            : Response::HTTP_UNAUTHORIZED;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
    }
}
