<?php

namespace App\Controller;


use App\Entity\Event;
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

        return $this->render('default/index.html.twig', [
            'sortie' => $sortie,
        ]);
    }
}
