<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\State;
use App\Form\EventType;
use App\Service\StateEnum;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/event/create", name="event_create")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    public function addNewEvent(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $event = new Event();
        $formEvent = $this->createForm(EventType::class, $event);
        $formEvent->handleRequest($request);

        if ($formEvent->isSubmitted())
        {
            //Gestion de la date de début
            $heureDebut = $request->get("event")["heureDebut"];
            $event->setDateDebut($event->getDateDebut()->modify("+" . $heureDebut["hour"] . " hour")->modify("+" . ($heureDebut["minute"] - 10) . " minutes"));

            //Gestion de la date de clôture
            $heureCloture = $request->get("event")["heureCloture"];
            $event->setDateDebut($event->getDateDebut()->modify("+" . $heureCloture["hour"] . " hour")->modify("+" . ($heureCloture["minute"] - 10) . " minutes"));

            if ($formEvent->isValid())
            {
                $event->setOrganisator($this->getUser());
                $event->setSite($this->getUser()->getSite());

                $em->persist($event);
                $em->flush();
                return $this->redirectToRoute("default_index");
            }
        }

        return $this->render('event/new.html.twig', [
            'form' => $formEvent->createView(),
        ]);
    }
}
