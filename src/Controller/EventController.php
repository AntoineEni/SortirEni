<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\CancelEventType;
use App\Form\EventType;
use App\Form\LocationType;
use App\Service\CheckEvent;
use App\Service\MailerService;
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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Manage all event redirection and function ajax
 * Class EventController
 * @package App\Controller
 */
class EventController extends AbstractController
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
     * Add a new event
     * @Route("/event/create", name="event_add", methods={"GET","POST"})
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
        $formLocation = $this->createForm(LocationType::class, new Location());
        $formEvent->handleRequest($request);

        if ($formEvent->isSubmitted() && $formEvent->isValid()) {
            try {
                $event->setOrganisator($this->getUser());
                $event->setSite($this->getUser()->getSite());

                $em->persist($event);
                $em->flush();

                $this->addFlash("success", $this->translator->trans("event.new.success"));
                return $this->redirectToRoute("event_detail", array("id" => $event->getId()));
            }catch (Exception $e){
                $this->addFlash("danger", $this->translator->trans("app.baderror") . " : " . $this->translator->trans("app.trylater"));
            }
        }

        return $this->render('event/new.html.twig', [
            'form' => $formEvent->createView(),
            'formLocation' => $formLocation->createView(),
        ]);
    }

    /**
     * Show details of an event
     * @Route("/event/{id}", name="event_detail", requirements={"id"="\d+"}, methods={"GET"})
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
            throw $this->createNotFoundException($this->translator->trans("event.notfound"));
        }

        return $this->render("event/detail.html.twig", array(
            "event" => $event,
            "actions" => $this->checkEvent->getListAction($this->getUser(), $event),
        ));
    }

    /**
     * Allow to edit an event
     * @Route("/event/{id}/edit", name="event_edit", requirements={"id"="\d+"}, methods={"GET","POST"})
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

        //Check if user can edit this event
        try {
            $this->checkEvent->canEditThisEvent($this->getUser(), $event, true);
        } catch (Exception $e) {
            $this->addFlash("danger", $this->translator->trans("app.baderror") . " : " . $this->translator->trans("app.trylater"));
            return $this->redirectToRoute("event_detail", array("id" => $id));
        }

        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            try {
                $em->persist($event);
                $em->flush();

                $this->mailerService->sendAfterEdit($event);

                $this->addFlash("success", $this->translator->trans("event.edit.success"));
                return $this->redirectToRoute("event_detail", array("id" => $id));
            } catch (Exception $e) {
                $this->addFlash("danger", $this->translator->trans("app.baderror") . " : " . $this->translator->trans("app.trylater"));
            }
        }

        return $this->render("event/edit.html.twig", array(
            "formEvent" => $eventForm->createView(),
            "event" => $event,
        ));
    }

    /**
     * Publish an event
     * @Route("/event/{id}/publish", name="event_publish", requirements={"id"="\d+"}, methods={"POST"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function publishEvent($id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => $this->translator->trans("event.publish.success"));

        $event = $em->getRepository(Event::class)->find($id);

        try {
            //check if user can publish this event
            $this->checkEvent->canPublishThisEvent($this->getUser(), $event, true);

            $event->setState(StateEnum::STATE_OPEN);

            $em->persist($event);
            $em->flush();

            $this->mailerService->sendToAllAfterPublish($event);
        } catch (Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }

    /**
     * Remove an event
     * @Route("/event/{id}/remove", name="event_remove", requirements={"id"="\d+"}, methods={"POST"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function removeEvent($id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => $this->translator->trans("event.remove.success"));

        $event = $em->getRepository(Event::class)->find($id);

        try {
            //check if user can remove this event
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
     * Cancel an event
     * @Route("/event/{id}/cancel", name="event_cancel", requirements={"id"="\d+"}, methods={"GET","POST"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function cancelEvent($id, Request $request,  EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);

        //Check if user can cancel this event
        try {
            $this->checkEvent->canCancelThisEvent($this->getUser(), $event, true);
        } catch (Exception $e) {
            $this->addFlash("danger", $this->translator->trans("app.baderror") . " : " . $this->translator->trans("app.trylater"));
            return $this->redirectToRoute("event_detail", array("id" => $id));
        }

        $eventForm = $this->createForm(CancelEventType::class, $event);
        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            try {
                $event->setState(StateEnum::STATE_CANCELED);

                $em->persist($event);
                $em->flush();

                $this->addFlash("success", $this->translator->trans("event.cancel.success"));
                return $this->redirectToRoute("event_detail", array("id" => $id));
            } catch (Exception $e) {
                $this->addFlash("danger", $this->translator->trans("app.baderror") . " : " . $this->translator->trans("app.trylater"));
            }
        }

        return $this->render("event/cancel.html.twig", array(
            "formEvent" => $eventForm->createView(),
            "event" => $event,
        ));
    }
}
