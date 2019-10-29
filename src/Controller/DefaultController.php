<?php

namespace App\Controller;


use App\Entity\Event;
use App\Form\EventFiltreType;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\CheckEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $checkEvent;

    public function __construct(CheckEvent $checkEvent)
    {
        $this->checkEvent = $checkEvent;
    }

    /**
     * @Route("/", name="default_index")
     */
    public function index()
    {
        $sortie = $this->getDoctrine()->getRepository(Event::class)->eventWhitNumberSubcriptyion($this->getUser());

        $event = new Event();
        $formEvent = $this->createForm(EventFiltreType::class, $event);

        foreach ($sortie as $key => $value) {
            $eventAction = $this->getDoctrine()->getRepository(Event::class)->find($value['id']);
            $sortie[$key]['actions'] = $this->checkEvent->getListAction($this->getUser(), $eventAction);
        }

        return $this->render('default/index.html.twig', [
            'sortie' => $sortie,
            'formEvent'=>$formEvent->createView(),
        ]);
    }
}
