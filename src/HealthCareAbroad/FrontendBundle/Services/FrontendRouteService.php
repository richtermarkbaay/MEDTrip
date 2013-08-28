<?php
/**
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\FrontendBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\FrontendBundle\Entity\FrontendRoute;

/**
 * Frontend route service. Handles routing of dynamic URLs in the frontend search.
 */
class FrontendRouteService
{

    const COMBINED_SEARCH_ROUTE_NAME = 'frontend_search_combined';

    /**
     * @var array
     */
    private static $storage = array();

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     *
     * @var unknown
     */
    private $routeResolver;

    private $logContext=array('Frontend Route');

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Match a URI to listed frontend dynamic routes
     * First step is to check if session variable $dynamicRoute is set, which means this has been directed from search form.
     * If not set in session, we next check the cookie for previously set urls.
     * If not set in cookie, we now refer to the database
     *
     * @param string $uri
     * @return FrontendRoute
     */
    public function match($uri)
    {
        // check first if this uri has already been stored in static storage
        if (array_key_exists($uri, static::$storage)) {
            return static::$storage[$uri];
        }

        $route = null;
//         if (is_null($route = $this->matchFromSession($uri))) {
//             if (is_null($route = $this->matchFromCookie($uri))) {
                $route = $this->matchFromDatabase($uri);
//             }
//         }

        if ($route) {
            // save in local storage
            static::$storage[$uri] = $route;
        }

        return $route;
    }

    /**
     * Tries to add a route (URI) to the table of known dynamic routes. Will
     * validate first if the route can resolve to a working page. Returns
     * FrontendRoute and adds the route if valid, otherwise returns null.
     *
     * @param string $uri
     * @return \HealthCareAbroad\FrontendBundle\Entity\FrontendRoute
     * @todo Refactor
     */
    public function addRoute($uri)
    {
        $variables = $this->getVariablesFromUri($uri);

        if (is_null($variables)) {
            return null;
        }

        $route = new FrontendRoute();
        $route->setController($this->extrapolateControllerFromVariables($variables));
        $route->setUri($uri);
        $route->setVariables(json_encode($variables));
        $route->setStatus(1);

        $em = $this->doctrine->getManager();
        $em->persist($route);
        $em->flush($route);

        return $route;
    }

    /**
     * @todo: move to an external class
     * @param array $variables
     * @return string
     */
    private function extrapolateControllerFromVariables(array $variables)
    {
        $controller = '';

        switch (count($variables)) {

            case 2:
                $controller = 'FrontendBundle:Default:listCountrySpecialization';

                break;

            case 3:
                if (isset($variables['cityId'])) {
                    $controller = 'FrontendBundle:Default:listCitySpecialization';
                } else if (isset($variables['subSpecializationId'])) {
                    $controller = 'FrontendBundle:Default:listCountrySubSpecialization';
                } elseif (isset($variables['treatmentId'])) {
                    $controller = 'FrontendBundle:Default:listCountryTreatment';
                }

                break;

            case 4:
                if (isset($variables['treatmentId'])) {
                    if (isset($variables['cityId']))
                        $controller = 'FrontendBundle:Default:listCityTreatment';
                    else
                        $controller = 'FrontendBundle:Default:listCountryTreatment';

                } else if (isset($variables['cityId'])) {
                    $controller = 'FrontendBundle:Default:listCitySubSpecialization';
                }

                break;

            case 5:
                $controller = 'FrontendBundle:Default:listCityTreatment';

                break;

            default:
                assert('Unreachable code.');
        }

        return $controller;
    }

    // Possible routes:
    //
    // /<country>/<city>/<specialization> (3)
    // /<country>/<city>/<specialization>/<subspecialization> (4)
    // /<country>/<city>/<specialization>/<treatment>/treatment (5)
    // /<country>/<specialization> (2)
    // /<country>/<specialization>/<subspecialization> (3)
    // /<country>/<specialization>/<treatment>/treatment (4)

    /**
     * @todo: move to an external class; refactor
     * @param string $uri
     * @return NULL|array
     */
    private function getVariablesFromUri($uri)
    {
        $variables = null;

        $country = null;
        $city = null;
        $center = null;
        $clinic = null;
        $treatment = null;
        $page = null; //TODO\

        // ALGORITHMN (BRUTE FORCE APPROACH)
        // 1. get number of tokens
        // 2. tokens = 2
        //       sanity check country and center MATCH /country/center
        // 3. tokens = 3
        //       test if second token is a center
        //       if true sanity check third token is a treatment MATCH /country/center/treatment
        // 4. tokens = 4
        //
        $tokens = explode('/', urldecode(substr($uri, 1)));
        $tokenCount = count($tokens);

        if (count($tokens) < 2 || count($tokens) > 5) {
            return null;
        }

        // first token should always be a country
        if (is_null($country = $this->doctrine->getRepository('HelperBundle:Country')->findOneBy(array('slug' => $tokens[0])))) {
            return null;
        }

        //MATCHED /country/...
        // second token can either be a specialization or a city
        if ($specialization = $this->doctrine->getRepository('TreatmentBundle:Specialization')->findOneBy(array('slug' => $tokens[1]))) {
            if ($tokenCount == 2) {
                // MATCHED /country/specialization
                $variables = array('countryId' => $country->getId(), 'specializationId' => $specialization->getId());

            } else if ($tokenCount == 3) {
                if ($subSpecialization = $this->doctrine->getRepository('TreatmentBundle:SubSpecialization')->findOneBy(array('slug' => $tokens[2]))) {
                    // MATCHED /country/specialization/subSpecialization
                    $variables = array(
                                    'countryId' => $country->getId(),
                                    'specializationId' => $specialization->getId(),
                                    'subSpecializationId' => $subSpecialization->getId()
                    );
                } else {
                    return null;
                }
            } else if ($tokenCount == 4) {
                if ($tokens[3] == 'treatment') {
                    if ($treatment = $this->doctrine->getRepository('TreatmentBundle:Treatment')->findOneBy(array('slug'=> $tokens[2]))) {
                        //matched /country/specialization/treatment/'treatment'
                        $variables = array(
                                        'countryId' => $country->getId(),
                                        'specializationId' => $specialization->getId(),
                                        'treatmentId' => $treatment->getId()
                        );
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }

        } else if ($city = $this->doctrine->getRepository('HelperBundle:City')->findOneBy(array('slug' => $tokens[1]))) {
            //MATCHED /country/city/...
            // third token should always be a specialization
            if (is_null($specialization = $this->doctrine->getRepository('TreatmentBundle:Specialization')->findOneBy(array('slug' => $tokens[2])))) {
                return null;
            }

            if ($tokenCount == 3) {
                //MATCHED /country/city/specialization
                $variables = array(
                                'countryId' => $country->getId(),
                                'cityId' => $city->getId(),
                                'specializationId' => $specialization->getId()
                );
            } else if ($tokenCount == 4) {
                if ($subSpecialization = $this->doctrine->getRepository('TreatmentBundle:SubSpecialization')->findOneBy(array('slug' => $tokens[3]))) {
                    //MATCHED /country/city/specialization/subSpecialization
                    $variables = array(
                                    'countryId' => $country->getId(),
                                    'cityId' => $city->getId(),
                                    'specializationId' => $specialization->getId(),
                                    'subSpecializationId' => $subSpecialization->getId()
                    );
                } else {
                    return null;
                }

            } else if ($tokenCount == 5) {
                if ($tokens[4] == 'treatment') {
                    if ($treatment = $this->doctrine->getRepository('TreatmentBundle:Treatment')->findOneBy(array('slug'=> $tokens[3]))) {
                        //matched /country/city/specialization/treatment/'treatment'
                        $variables = array(
                                        'countryId' => $country->getId(),
                                        'cityId' => $city->getId(),
                                        'specializationId' => $specialization->getId(),
                                        'treatmentId' => $treatment->getId()
                        );
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }

        return $variables;
    }

    /**
     * Match a frontend route by session key
     *
     * @param string $uri
     * @return FrontendRoute
     */
    private function matchFromSession($uri)
    {
        $routeObj = null;
        if ($vars = $this->session->get(\md5($uri), null)) {

            $routeObj = new FrontendRoute();
            $routeObj->setUri($uri);
            $routeObj->setController($this->extrapolateControllerFromVariables(json_decode($vars, true)));
            $routeObj->setVariables($vars);
            $this->logger->info("Matched uri '{$uri}' from session with variables: {$vars}");
        }

        return $routeObj;
    }

    /**
     * Find a matching frontend route in the cookie by uri
     *
     * @param string $uri
     * @return FrontendRoute
     */
    private function matchFromCookie($uri)
    {
        $routeObj = null;
        if ($vars = $this->request->cookies->get(\md5($uri),null)) {
            $routeObj = new FrontendRoute();
            $routeObj->setUri($uri);
            $routeObj->setController($this->extrapolateControllerFromVariables(json_decode($vars, true)));
            $routeObj->setVariables($vars);
            $this->logger->info("Matched uri '{$uri}' from cookie with variables: {$vars}");
        }

        return $routeObj;
    }

    /**
     * Find a record in frontend_routes by uri
     *
     * @param string $uri
     * @return FrontendRoute
     */
    private function matchFromDatabase($uri)
    {
        $routeObj = $this->doctrine->getRepository('FrontendBundle:FrontendRoute')->findOneBy(array('uri' => $uri));

        if ($routeObj) {
            $this->logger->info("Matched uri '{$uri}' from database with variables: {$routeObj->getVariables()}");
        }

        return $routeObj;
    }
}