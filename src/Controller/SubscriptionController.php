<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Service\CheckEvent;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    private $checkEvent;
    private $mailerService;

    public function __construct(CheckEvent $checkEvent, MailerService $mailerService)
    {
        $this->checkEvent = $checkEvent;
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/subscription/add/{id}", name="subscription_add", requirements={"id"="\d+"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function addNewSubscription($id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => "Inscription réussie !");

        $event = $em->getRepository(Event::class)->find($id);

        try {
            $this->checkEvent->canSubscribeToThisEvent($this->getUser(), $event, true);

            $subscription = new Subscription();
            $subscription->setParticipant($this->getUser())->setEvent($event)->setDateInscription(new \DateTime());

            $em->persist($subscription);
            $em->flush();

            $this->checkEvent->editStatusAfterSubscription($event);
            $this->mailerService->sendAfterSubscription($event, $subscription);
        } catch (\Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/subscription/remove/{id}", name="subscription_remove", requirements={"id"="\d+"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function removeExistingSubscription($id, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => "Désinscription réussie !");

        $event = $em->getRepository(Event::class)->find($id);

        try {
            $this->checkEvent->canUnsubscribeToThisEvent($this->getUser(), $event, true);

            $subscription = $em->getRepository(Subscription::class)->findOneBy(array("participant" => $this->getUser(), "event" => $event));

            $em->remove($subscription);
            $em->flush();

            $this->checkEvent->editStatusAfterSubscription($event);
            $this->mailerService->sendAfterSubscription($event, $subscription);
        } catch (\Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
