<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\RepositoryResponse;
use App\Service\ResponseService\Constants;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\ViewObjects\Address as AddressView;

/**
 * @Route("/api/address", name="address")
 */
class AddressController extends CoreController
{
    /**
     * @return JsonResponse
     * @Route("/insert", name="_insert", methods={"POST"})
     */
    public function addAddress()
    {
        $pService = new UserService($this->getContainer());
        $data = json_decode($this->getRequest()->getContent());

        $insertResponse = $pService->addAdress($data,$this->getUser());
        if(!$insertResponse->isSuccess() || $insertResponse->getException())
        {
            return $this->getResponseService()->toJsonResponse($insertResponse->getException(),Constants::MSG_500_0000,$insertResponse->getMessage());
        }

        return $this->getResponseService()->toJsonResponse(AddressView::create($insertResponse->getResponse())->toArray(),Constants::MSG_200_0000,$insertResponse->getMessage());
    }

    /**
     * @Route("/", name="_list", methods={"GET"})
     */
    public function listAddress()
    {
        $addressRepo = $this->getEntityManager()->getRepository(Address::class);
        /** @var RepositoryResponse $addressResponse */
        $addressResponse = $addressRepo->findByWithResponse(['user' => $this->getUser()]);
        if($addressResponse->getException())
        {
            return $this->getResponseService()->toJsonResponse($addressResponse->getException(),Constants::MSG_500_0000,$addressResponse->getMessage());
        }
        $addressList = [];
        foreach ($addressResponse->getResponse() as $address)
        {
            $addressList[] = AddressView::create($address)->toArray();
        }

        return $this->getResponseService()->toJsonResponse($addressList, Constants::MSG_200_0000, $addressResponse->getMessage());
    }
}
