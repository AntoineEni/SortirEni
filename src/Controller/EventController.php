<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\State;
use App\Form\CancelEventType;
use App\Form\EventType;
use App\Form\LocationType;
use App\Service\CheckEvent;
use App\Service\StateEnum;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EventController extends AbstractController
{
    private $checkEvent;

    public function __construct(CheckEvent $checkEvent)
    {
        $this->checkEvent = $checkEvent;
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

        if ($formEvent->isSubmitted() && $formEvent->isValid()) {
            try {
                $event->setOrganisator($this->getUser());
                $event->setSite($this->getUser()->getSite());
                $em->persist($event);
                $em->flush();
                $this->addFlash("success", "Event create with success");
                return $this->redirectToRoute("event_detail", array("id" => $event->getId()));
            }catch (\Exception $e){
                $this->addFlash("danger", $e->getMessage());
            }
         }

        $formLocation = $this->createForm(LocationType::class, new Location());

        return $this->render('event/new.html.twig', [
            'form' => $formEvent->createView(),
            'formLocation' => $formLocation->createView(),
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
            "actions" => $this->checkEvent->getListAction($this->getUser(), $event),
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
            $this->checkEvent->canEditThisEvent($this->getUser(), $event, true);
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
                $this->addFlash("danger", "Error");
            }
        }

        return $this->render("event/edit.html.twig", array(
            "formEvent" => $eventForm->createView(),
            "event" => $event,
        ));
    }

    /**
     * @Route("/event/{id}/publish", name="event_publish", requirements={"id"="\d+"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function publishEvent($id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => "Publication réussie !");

        $event = $em->getRepository(Event::class)->find($id);

        try {
            $this->checkEvent->canPublishThisEvent($this->getUser(), $event);

            $event->setState(StateEnum::STATE_OPEN);

            $em->persist($event);
            $em->flush();
        } catch (Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/event/{id}/remove", name="event_remove", requirements={"id"="\d+"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function removeEvent($id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => "Suppression réussie !");

        $event = $em->getRepository(Event::class)->find($id);

        try {
            $this->checkEvent->canRemoveThisEvent($this->getUser(), $event);

            $em->remove($event);
            $em->flush();
        } catch (Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/event/{id}/cancel", name="event_cancel", requirements={"id"="\d+"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function cancelEvent($id, Request $request,  EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);

        try {
            $this->checkEvent->canCancelThisEvent($this->getUser(), $event, true);
        } catch (Exception $e) {
            $this->addFlash("danger", $e->getMessage());
            return $this->redirectToRoute("event_detail", array("id" => $id));
        }

        $eventForm = $this->createForm(CancelEventType::class, $event);
        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            try {
                $event->setState(StateEnum::STATE_CANCELED);

                $em->persist($event);
                $em->flush();
                $this->addFlash("success", "Event edit with success");
                return $this->redirectToRoute("event_detail", array("id" => $id));
            } catch (\Exception $e) {
                $this->addFlash("danger", $e->getMessage());
            }
        }

        return $this->render("event/cancel.html.twig", array(
            "formEvent" => $eventForm->createView(),
            "event" => $event,
        ));
    }
}
