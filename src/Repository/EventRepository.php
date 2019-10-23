<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function  eventWhitNumberSubcriptyion(User $user){
        return $this->createQueryBuilder('e')
            ->addSelect('s')
            ->addSelect('o')
            ->addSelect('et')
            ->addSelect('si')
            ->select('si.name as site,et.label, o.username, e.name, e.dateCloture, e.dateDebut, e.description, e.duration, e.inscriptionsMax, COUNT(s) as nombreDeParticipant')
            ->addSelect("CASE WHEN (sP.id IS NULL) THEN false ELSE true END AS Participation")
            ->innerJoin('e.organisator','o')
            ->innerJoin('e.etat','et')
            ->innerJoin('e.site','si')
            ->leftJoin('e.subscriptions','s')
            ->leftJoin('e.subscriptions','sP', Expr\Join::WITH,'sP.participant = :user')
            ->setParameter('user', $user)
            ->groupBy('e.id')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
