<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    /**
     * @Route("/location/add", name="location_add")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $response = array("ok" => true, "response" => "Ajout du lieu réussi !");

        try {
            $location = new Location();
            $location->setName($request->get('name'))->setStreet($request->get('street'))
                ->setLatitude(floatval($request->get('latitude')))->setLongitude(floatval($request->get('longitude')))
                ->setCity($em->getRepository(City::class)->find($request->get('city')));

            $em->persist($location);
            $em->flush();

            $response["location"] = array($location->getId(), $location->getName());
        } catch (Exception $e) {
            $response["ok"] = false;
            $response["response"] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
