<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use App\Service\StateEnum;
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

    /**
     * Return events with few more information
     * @param User $user
     * @return array
     */
    public function eventWhitNumberSubscription(User $user) {
        return $this->createQueryBuilder('e')
            ->addSelect('s')
            ->addSelect('o')
            ->addSelect('si')
            ->select('si.name as site, e.id, e.state, o.username, e.name, e.dateCloture, e.dateDebut,
             e.description, e.duration, e.inscriptionsMax, COUNT(s) as nombreDeParticipant')
            ->addSelect("CASE WHEN (sP.id IS NULL) THEN false ELSE true END AS Participation")
            ->innerJoin('e.organisator','o')
            ->innerJoin('e.site','si')
            ->leftJoin('e.subscriptions','s')
            ->leftJoin('e.subscriptions','sP',
                Expr\Join::WITH,'sP.participant = :user')
            ->where('e.state != :state')
            ->andWhere('((e.organisator = :user AND e.state = :stateC) OR (e.state != :stateC))')
            ->setParameter('state', StateEnum::STATE_ARCHIVED)
            ->setParameter('user', $user)
            ->setParameter('stateC',StateEnum::STATE_CREATE)
            ->groupBy('e.id')
            ->getQuery()
            ->getArrayResult();
    }
}
