<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

class UserRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkUser($email, $username){
        $em = $this->getEntityManager();
        $qb = $this->createQueryBuilder('u');

        $q = $qb->select('u')
            ->where($qb->expr()->orX($qb->expr()->eq("u.email",":email"),$qb->expr()->eq("u.username",':username')))
            ->setParameters(['email' => $email, 'username' => $username])
            ->setMaxResults(1)->getQuery();

        return new RepositoryResponse($q->getOneOrNullResult());
    }
}
