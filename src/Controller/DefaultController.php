<?php

namespace App\Controller;


use App\Entity\Event;
use App\Form\EventFiltreType;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\CheckEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Use to manage the default pages
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    private $checkEvent;

    public function __construct(CheckEvent $checkEvent)
    {
        $this->checkEvent = $checkEvent;
    }

    /**
     * Send to the home page
     * @Route("/", name="default_index")
     * @throws \Exception
     */
    public function index()
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        //Get events with few more information
        $events = $this->getDoctrine()->getRepository(Event::class)->eventWhitNumberSubscription($this->getUser());

        $formEventFilter = $this->createForm(EventFiltreType::class, new Event());

        //Foreach event, get the list of available actions
        foreach ($events as $key => $value) {
            $eventAction = $this->getDoctrine()->getRepository(Event::class)->find($value['id']);
            $events[$key]['actions'] = $this->checkEvent->getListAction($this->getUser(), $eventAction);
        }

        return $this->render('default/index.html.twig', [
            'events' => $events,
            'formEventFilter' => $formEventFilter->createView(),
        ]);
    }
}
