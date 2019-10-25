<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckEventAction
{
    private $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function canSubscribeToThisEvent(User $user, Event $event) {
        if ($event == null) {
            throw new NotFoundHttpException("Not found event");
        } else if ($event->getOrganisator() === $user) {
            throw new BadRequestHttpException("Cannot subscribe to your own event");
        } else if ($event->getDateCloture() > new \DateTime()) {
            throw new BadRequestHttpException("Closure date has been reach, you can subscribe no more");
        } else if ($event->getState() != StateEnum::STATE_OPEN) {
            throw new BadRequestHttpException("Cannot subscribe to not open event");
        } else if ($this->subscriptionRepository->findOneBy(array("participant" => $user, "event" => $event))) {
            throw new InvalidArgumentException("You've already subscribe to this event");
        }

        return true;
    }
}