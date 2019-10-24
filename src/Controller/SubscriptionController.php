<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Service\StateEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * @Route("/subscription/add/{id}", name="subscription_add", requirements={"id"="\d+"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function addNewSubscription($id, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true);

        $event = $em->getRepository(Event::class)->find($id);

        try {
            if ($event == null) {
                throw $this->createNotFoundException("Not found event");
            } else if ($event->getOrganisator() == $this->getUser()) {
                throw new BadRequestHttpException("Cannot subscriptor to your own event");
            } else if ($event->getState() != StateEnum::STATE_OPEN) {
                throw new BadRequestHttpException("Cannot subscribe to not open event");
            } else if ($em->getRepository(Subscription::class)->findOneBy(array("participant" => $this->getUser(), "event" => $event))) {
                throw new InvalidArgumentException("You've already subscribe to this event");
            } else {
                $subscription = new Subscription();
                $subscription->setParticipant($this->getUser())->setEvent($event)->setDateInscription(new \DateTime());

                $em->persist($subscription);
                $em->flush();
            }
        } catch (\Exception $e) {
            $response["ok"] = false;
            $response["errors"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
