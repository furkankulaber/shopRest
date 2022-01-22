<?php

namespace App\ViewObjects;

use App\Entity\User as UserEntity;
use App\ViewObjects\BaseViewObject;

class User extends BaseViewObject
{

    private UserEntity $user;

    public function __construct(UserEntity $user)
    {
        $this->user = $user;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->user->getId(),
            'username' => $this->user->getUsername(),
            'email' => $this->user->getEmail()
        ];
    }

    public static function create($data)
    {
        return new self($data);
    }
}
