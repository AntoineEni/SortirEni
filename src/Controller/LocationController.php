<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Form\EventType;
use App\Form\LocationType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LocationController extends AbstractController
{
    /**
     * @Route("/location/add", name="location_add")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     */
    public function add(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => "Ajout du lieu réussi !");

        try {
            $location = new Location();
            $formLocation = $this->createForm(LocationType::class, $location);
            $formLocation->handleRequest($request);

            if ($formLocation->isSubmitted() && $formLocation->isValid()) {
                $em->persist($location);
                $em->flush();

                $response["location"] = array($location->getId(), $location->getName());
            } else {
                throw new InvalidCsrfTokenException("Invalid CSRF Token");
            }
        } catch (Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
