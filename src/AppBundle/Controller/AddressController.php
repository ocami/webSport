<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Entity\Competition;
use AppBundle\Services\EntityService;
use AppBundle\Services\RanckService;
use Proxies\__CG__\AppBundle\Entity\AddressCompetitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class AddressController extends Controller
{
  
    /**
     * @Route("address/getCities", name="address_getCities")
     */
    public function getCities(Request $request)
    {
        $cities = $this->getDoctrine()->getRepository(Address::class)->citiesToString();

        return new JsonResponse($cities);
    }

    /**
     * @Route("address/getCitiesData", name="address_getCitiesData")
     */
    public function getCitiesData(Request $request)
    {

        $villeSlug = $request->query->get('ville_slug');

        $city = $this->getDoctrine()->getRepository(Address::class)->citiesDataToString($villeSlug);

        return new JsonResponse($city);
    }

    /**
     * @Route("address/getCitiesSlugByDep", name="address_getCitiesSlugByDep")
     */
    public function getCitiesSlugByDep(Request $request)
    {

        $dep = $request->query->get('dep');

        $city = $this->getDoctrine()->getRepository(Address::class)->citiesByDep($dep);

        return new JsonResponse($city);
    }

}