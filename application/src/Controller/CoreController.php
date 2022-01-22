<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use RuntimeException;
use App\Service\ResponseService\Service as ResponseService;


class CoreController extends AbstractController
{
    /** @var ResponseService */
    private ResponseService $responseService;

    /** @var Request */
    private Request $request;

    private EntityManagerInterface $entityManager;

    /**
     * CoreController constructor.
     * @param RequestStack $requestStack
     * @param ResponseService $responseService
     */
    public function __construct(RequestStack $requestStack, ResponseService $responseService, ContainerInterface $container)
    {
        if (null === $requestStack->getMasterRequest()) {
            throw new RuntimeException('Internal Error');
        }
        $this->container = $container;
        $this->request = $requestStack->getMasterRequest();
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->responseService = $responseService;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * @return ResponseService
     */
    public function getResponseService(): ResponseService
    {
        return $this->responseService;
    }

}
