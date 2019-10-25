<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Subscription;
use App\Service\CheckEventAction;
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
    private $checkEventAction;

    public function __construct(CheckEventAction $checkEventAction)
    {
        $this->checkEventAction = $checkEventAction;
    }

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
            $this->checkEventAction->canSubscribeToThisEvent($this->getUser(), $event);

            $subscription = new Subscription();
            $subscription->setParticipant($this->getUser())->setEvent($event)->setDateInscription(new \DateTime());

            $em->persist($subscription);
            $em->flush();
        } catch (\Exception $e) {
            $response["ok"] = false;
            $response["errors"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
