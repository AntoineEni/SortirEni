<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Service\CheckEvent;
use App\Service\MailerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Manage all Subscription
 * Class SubscriptionController
 * @package App\Controller
 */
class SubscriptionController extends AbstractController
{
    private $checkEvent;
    private $mailerService;
    private $translator;

    public function __construct(CheckEvent $checkEvent, MailerService $mailerService, TranslatorInterface $translator)
    {
        $this->checkEvent = $checkEvent;
        $this->mailerService = $mailerService;
        $this->translator = $translator;
    }

    /**
     * Add a subscription
     * @Route("/subscription/add/{id}", name="subscription_add", requirements={"id"="\d+"}, methods={"POST"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function addNewSubscription($id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => $this->translator->trans("subscription.add.success"));

        $event = $em->getRepository(Event::class)->find($id);

        try {
            //check if user can subscribe
            $this->checkEvent->canSubscribeToThisEvent($this->getUser(), $event, true);

            $subscription = new Subscription();
            $subscription->setParticipant($this->getUser())->setEvent($event)->setDateInscription(new DateTime());

            $em->persist($subscription);
            $em->flush();

            $this->checkEvent->editStatusAfterSubscription($event);
            $this->mailerService->sendAfterSubscription($event, $subscription);
        } catch (Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * Remove a subscription
     * @Route("/subscription/remove/{id}", name="subscription_remove", requirements={"id"="\d+"}, methods={"POST"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function removeExistingSubscription($id, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => $this->translator->trans("subscription.remove.success"));

        $event = $em->getRepository(Event::class)->find($id);

        try {
            //Check if user can unsubscribe
            $this->checkEvent->canUnsubscribeToThisEvent($this->getUser(), $event, true);

            $subscription = $em->getRepository(Subscription::class)->findOneBy(array("participant" => $this->getUser(), "event" => $event));

            $em->remove($subscription);
            $em->flush();

            $this->checkEvent->editStatusAfterSubscription($event);
            $this->mailerService->sendAfterSubscription($event, $subscription);
        } catch (Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
