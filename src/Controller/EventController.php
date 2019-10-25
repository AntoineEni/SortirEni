<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\State;
use App\Form\EventType;
use App\Service\CheckEvent;
use App\Service\StateEnum;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EventController extends AbstractController
{
    private $checkEventAction;

    public function __construct(CheckEvent $checkEventAction)
    {
        $this->checkEventAction = $checkEventAction;
    }

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

        if ($formEvent->isSubmitted() && $formEvent->isValid())
        {
            $event->setOrganisator($this->getUser());
            $event->setSite($this->getUser()->getSite());

            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute("default_index");
        }

        return $this->render('event/new.html.twig', [
            'form' => $formEvent->createView(),
        ]);
    }

    /**
     * @Route("/event/{id}", name="event_detail", requirements={"id"="\d+"})
     * @param $id
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function detailEvent($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);

        if ($event == null) {
            throw $this->createNotFoundException("Not found event");
        }

        return $this->render("event/detail.html.twig", array(
            "event" => $event,
            "actions" => $this->checkEventAction->getListAction($this->getUser(), $event),
        ));
    }

    /**
     * @Route("/event/{id}/edit", name="event_edit", requirements={"id"="\d+"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    public function editEvent($id, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);

        try {
            $this->checkEventAction->canEditThisEvent($this->getUser(), $event, true);
        } catch (Exception $e) {
            $this->addFlash("danger", $e->getMessage());
            return $this->redirectToRoute("event_detail", array("id" => $id));
        }

        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            try {
                $em->persist($event);
                $em->flush();
                $this->addFlash("success", "Event edit with success");
                return $this->redirectToRoute("event_detail", array("id" => $id));
            } catch (\Exception $e) {
                $this->addFlash("danger", $e->getMessage());
            }
        }

        return $this->render("event/edit.html.twig", array(
            "formEvent" => $eventForm->createView(),
            "event" => $event,
        ));
    }
}
