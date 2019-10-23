<?php

namespace App\Controller;


use App\Entity\Event;
use App\Form\EventFiltreType;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     */
    public function index()
    {
        $sortie = $this->getDoctrine()->getRepository(Event::class)->eventWhitNumberSubcriptyion($this->getUser());

        $event = new Event();
        $formEvent = $this->createForm(EventFiltreType::class, $event);


        return $this->render('default/index.html.twig', [
            'sortie' => $sortie,
            'formEvent'=>$formEvent->createView(),
        ]);
    }
}
