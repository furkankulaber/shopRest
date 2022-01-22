<?php

namespace App\Service;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{

    private ContainerInterface $container;
    private UserRepository $userRepository;
    private AddressRepository $addressRepository;
    private ?UserPasswordEncoderInterface $encoder;

    public function __construct(ContainerInterface $container, $encoder = null)
    {
        $this->container = $container;
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine')->getManager();
        $this->userRepository = $em->getRepository(User::class);
        $this->addressRepository = $em->getRepository(Address::class);
        $this->encoder = $encoder;
    }

    public function registerUser($data)
    {
        $findResponse = $this->userRepository->checkUser($data->email, $data->username);
        if($findResponse->isSuccess() && $findResponse->getResponse() instanceof User){
            return new ServiceResponse(null,false,'Kullanıcı adı veya Mail adresi kayıtlı');
        }

        $password = $this->encoder->encodePassword(new User($data->username),$data->password);

        $insertData = [
            'email' => $data->email,
            'username' => $data->username,
            'password' => $password
        ];


        $insertResponse = $this->userRepository->insert($insertData);
        if(!$insertResponse->isSuccess()){
            return new ServiceResponse($insertResponse->getException(),false,'Bir hata oluştu, tekrar deneyin');
        }

        return new ServiceResponse($insertResponse->getResponse(),true,sprintf('%s mail adresli kullanıcı kayıt edildi',$data->email));


    }

    public function addAdress($data, $user)
    {
        $insert = [
            'user' => $user,
            'address' => $data->address,
            'title' => $data->title
        ];

        $insertResponse = $this->addressRepository->insert($insert);

        if(!$insertResponse->isSuccess()){
            return new ServiceResponse($insertResponse->getException(),false,'Bir hata oluştu, tekrar deneyin');
        }

        return new ServiceResponse($insertResponse->getResponse(),true,'İşleminiz başarıyla gerçekleştirilmiştir.');
    }
}
