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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Service use for check events status or available actions
 * Class CheckEvent
 * @package App\Service
 */
class CheckEvent
{
    private $subscriptionRepository;
    private $em;
    private $router;
    private $translator;

    /**
     * CheckEvent constructor.
     * @param SubscriptionRepository $subscriptionRepository
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(SubscriptionRepository $subscriptionRepository, EntityManagerInterface $em,
                                UrlGeneratorInterface $router, TranslatorInterface $translator)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * Check if user can subscribe to specific event
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canSubscribeToThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException($this->translator->trans("event.notfound"));
            } else if ($event->getOrganisator() === $user) {
                throw new BadRequestHttpException($this->translator->trans("subscription.add.error.organisator"));
            } else if ($event->getDateCloture() < new \DateTime()) {
                throw new BadRequestHttpException($this->translator->trans("subscription.add.error.datecloture"));
            } else if ($event->getState() != StateEnum::STATE_OPEN) {
                throw new BadRequestHttpException($this->translator->trans("subscription.add.error.state"));
            } else if ($this->subscriptionRepository->findOneBy(array("participant" => $user, "event" => $event))) {
                throw new InvalidArgumentException($this->translator->trans("subscription.add.error.already"));
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
     * Check if user can unsubscribe to specific event
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canUnsubscribeToThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException($this->translator->trans("event.notfound"));
            } else if ($event->getOrganisator() === $user) {
                throw new BadRequestHttpException($this->translator->trans("subscription.remove.error.organisator"));
            } else if ($event->getState() > StateEnum::STATE_CLOSE) {
                throw new BadRequestHttpException($this->translator->trans("subscription.remove.error.state"));
            } else if (!$this->subscriptionRepository->findOneBy(array("participant" => $user, "event" => $event))) {
                throw new InvalidArgumentException($this->translator->trans("subscription.remove.error.already"));
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
     * Check if user can edit a specific event
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canEditThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException($this->translator->trans("event.notfound"));
            } else if ($event->getOrganisator() !== $user) {
                throw new AccessDeniedException($this->translator->trans("event.edit.error.organisator"));
            } else if ($event->getState() != StateEnum::STATE_CREATE) {
                throw new BadRequestHttpException($this->translator->trans("event.edit.error.state"));
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
     * Check if user can publish a specific event
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canPublishThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException($this->translator->trans("event.notfound"));
            } else if ($event->getOrganisator() !== $user) {
                throw new AccessDeniedException($this->translator->trans("event.publish.error.organisator"));
            } else if ($event->getState() != StateEnum::STATE_CREATE) {
                throw new BadRequestHttpException($this->translator->trans("event.publish.error.state"));
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
     * Check if user can remove specific event
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canRemoveThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException($this->translator->trans("event.notfound"));
            } else if ($event->getOrganisator() !== $user) {
                throw new AccessDeniedException($this->translator->trans("event.remove.error.organisator"));
            } else if ($event->getState() !== StateEnum::STATE_CREATE) {
                throw new BadRequestHttpException($this->translator->trans("event.remove.error.state"));
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
     * Check if user can cancel a specific event
     * @param User $user
     * @param Event $event
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function canCancelThisEvent(User $user, Event $event, $throwException = false) {
        try {
            if ($event == null) {
                throw new NotFoundHttpException($this->translator->trans("event.notfound"));
            } else if ($event->getOrganisator() !== $user) {
                throw new AccessDeniedException($this->translator->trans("event.cancel.error.state"));
            } else if (!in_array($event->getState(), StateEnum::canCancel())) {
                throw new BadRequestHttpException($this->translator->trans("event.cancel.error.state"));
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
     * Get all actions a user can do with a specific event
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
                    "true"),
            "action_publish" =>
                array(
                    "canPublishThisEvent",
                    $this->router->generate("event_publish", array("id" => $event->getId())),
                    "true"),
            "action_remove" =>
                array(
                    "canRemoveThisEvent",
                    $this->router->generate("event_remove", array("id" => $event->getId())),
                    "true"),
            "action_cancel" =>
                array(
                    "canCancelThisEvent",
                    $this->router->generate("event_cancel", array("id" => $event->getId())),
                    "false"
                ));

        foreach ($functions as $key => $value) {
            if (call_user_func_array(array(__NAMESPACE__ . "\CheckEvent", $value[0]), array($user, $event))) {
                $actions[$key] = array("name" => $this->translator->trans(str_replace("_", ".", $key)),
                    "link" => $value[1], "ajax" => $value[2]);
            }
        }

        return $actions;
    }

    /**
     * Edit the status event after subscription or unsubscription
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