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
     * Return a user based on mail or username
     * @param $usernameOrEmail
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function logUserByLoginOrMail($usernameOrEmail) {
        return $this->createQueryBuilder("u")
            ->where("u.username = :query")
            ->orWhere("u.mail = :query")
            ->andWhere("u.isActif = :actif")
            ->setParameter("query", $usernameOrEmail)
            ->setParameter("actif", 1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Return the users to notify after a publication
     * @return mixed
     */
    public function toNotifyAfterPublish() {
        return $this->createQueryBuilder("u")
            ->where("u.isActif = :actif")
            ->setParameter("actif", 1)
            ->getQuery()->getResult();
    }

    /**
     * Return the users to notify after an edit
     * @return mixed
     */
    public function toNotifyAfterEdit() {
        return $this->createQueryBuilder("u")
            ->where("u.isActif = :actif")
            ->andWhere("u.isAdmin = :admin")
            ->setParameter("actif", 1)
            ->setParameter("admin", 1)
            ->getQuery()->getResult();
    }
}
