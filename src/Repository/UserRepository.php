<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $usernameOrEmail
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function logUserByLoginOrMail($usernameOrEmail) {
        return $this->createQueryBuilder("u")
            ->where("u.username = :query")
            ->orWhere("u.mail = :query")
            ->setParameter("query", $usernameOrEmail)
            ->getQuery()->getOneOrNullResult();
    }
}
