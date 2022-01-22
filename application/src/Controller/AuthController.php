<?php

namespace App\Controller;

use App\Service\ResponseService\Constants;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\ViewObjects\User as UserView;

class AuthController extends CoreController
{

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     * @Route("register", name="user_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $data = json_decode($this->getRequest()->getContent());
        if (empty($data->username) || empty($data->password) || empty($data->email)){
            return $this->getResponseService()->toJsonResponse(null, Constants::MSG_401_0010,'Invalid Username or Password or Email');
        }

        $pService = new UserService($this->getContainer(), $encoder);
        $sResponse = $pService->registerUser($data);
        if(!$sResponse->isSuccess()){
            return $this->getResponseService()->toJsonResponse(null,Constants::MSG_401_0010,$sResponse->getMessage());
        }

        return $this->getResponseService()->toJsonResponse(UserView::create($sResponse->getResponse())->toArray(), Constants::MSG_200_0000,$sResponse->getMessage());
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     * @Route("api/login", name="user_login")
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return $this->getResponseService()->withSessionToken($JWTManager->create($user))->toJsonResponse(UserView::create($user)->toArray());
    }

}
