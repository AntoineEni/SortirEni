<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CheckEvent
{
    private $subscriptionRepository;
    private $em;
    private $router;

    /**
     * CheckEvent constructor.
     * @param SubscriptionRepository $subscriptionRepository
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $router
     */
    public function __construct(SubscriptionRepository $subscriptionRepository, EntityManagerInterface $em, UrlGeneratorInterface $router)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canSubscribeToThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException("Not found event");
            } else if ($event->getOrganisator() === $user) {
                throw new BadRequestHttpException("Cannot subscribe to your own event");
            } else if ($event->getDateCloture() < new \DateTime()) {
                throw new BadRequestHttpException("Closure date has been reach, you can't subscribe no more");
            } else if ($event->getState() != StateEnum::STATE_OPEN) {
                throw new BadRequestHttpException("Cannot subscribe to not open event");
            } else if ($this->subscriptionRepository->findOneBy(array("participant" => $user, "event" => $event))) {
                throw new InvalidArgumentException("You've already subscribe to this event");
            }
        } catch (Exception $e) {
            if ($throwException) {
                throw $e;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canUnsubscribeToThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException("Not found event");
            } else if ($event->getOrganisator() === $user) {
                throw new BadRequestHttpException("Cannot unsubscribe to your own event");
            } else if ($event->getDateCloture() < new \DateTime()) {
                throw new BadRequestHttpException("Closure date has been reach, you can't unsubscribe no more");
            } else if ($event->getState() > StateEnum::STATE_CLOSE) {
                throw new BadRequestHttpException("Cannot unsubscribe to this event");
            } else if (!$this->subscriptionRepository->findOneBy(array("participant" => $user, "event" => $event))) {
                throw new InvalidArgumentException("You've not already subscribe to this event");
            }
        } catch (Exception $e) {
            if ($throwException) {
                throw $e;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canEditThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException("Not found event");
            } else if ($event->getOrganisator() !== $user) {
                throw new AccessDeniedException("Not allowed to edit this event");
            }
        } catch (Exception $e) {
            if ($throwException) {
                throw $e;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @param User $user
     * @param Event $event
     * @return array
     * @throws Exception
     */
    public function getListAction(User $user, Event $event) {
        $actions = array();

        $functions = array(
            "action_subscribe" =>
                array(
                    "canSubscribeToThisEvent",
                    $this->router->generate("subscription_add", array("id" => $event->getId())),
                    "true"),
            "action_edit" =>
                array(
                    "canEditThisEvent",
                    $this->router->generate("event_edit", array("id" => $event->getId())),
                    "false"),
            "action_unsubscribe" =>
                array(
                    "canUnsubscribeToThisEvent",
                    $this->router->generate("subscription_remove", array("id" => $event->getId())),
                    "true"
                ));

        foreach ($functions as $key => $value) {
            if (call_user_func_array(array(__NAMESPACE__ . "\CheckEvent", $value[0]), array($user, $event))) {
                $actions[$key] = array("link" => $value[1], "ajax" => $value[2]);
            }
        }

        return $actions;
    }

    /**
     * @param Event $event
     * @throws Exception
     */
    public function editStatusAfterSubscription(Event $event) {
        if ($event->getState() == StateEnum::STATE_OPEN &&
            ($event->getInscriptionsMax() == count($event->getSubscriptions()) || $event->getDateCloture() < new \DateTime())) {
            $event->setState(StateEnum::STATE_CLOSE);
        } else if ($event->getState() == StateEnum::STATE_CLOSE &&
            $event->getInscriptionsMax() > count($event->getSubscriptions()) &&
            $event->getDateCloture() >= new \DateTime()) {
            $event->setState(StateEnum::STATE_OPEN);
        }

        $this->em->persist($event);
        $this->em->flush();
    }
}