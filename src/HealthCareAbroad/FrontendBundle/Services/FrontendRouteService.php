<?php
/**
 * Frontend Route service
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\FrontendBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Bridge\Monolog\Logger;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\FrontendBundle\Entity\FrontendRoute;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FrontendRouteService
{

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
        if (is_null($route = $this->matchFromSession($uri))) {
            if (is_null($route = $this->matchFromCookie($uri))) {
                $route = $this->matchFromDatabase($uri);
            }
        }

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
        $route->setUri($uri);
        $route->setVariables($variables);
        $route->setStatus(1);

        $em = $this->doctrine->getManager();
        $em->persist($route);
        $em->flush($route);

        return $route;
    }

    /**
     * @todo refactor
     * @param string $uri
     * @return NULL|Ambigous <NULL, string>
     */
    private function getVariablesFromUri($uri)
    {
        $variables = null;

        $country = null;
        $city = null;
        $center = null;
        $clinic = null;
        $procedureType = null;

        // Possible routes:
        //
        // /country/city/center/clinic/treatment
        // /country/city/center/clinic
        // /country/city/center/treatment
        // /country/city/center
        // /country/center/treatment
        // /country/center

        // ALGORITHMN (BRUTE FORCE APPROACH)
        // 1. get number of tokens
        // 2. tokens = 2
        //       sanity check country and center MATCH /country/center
        // 3. tokens = 3
        //       test if second token is a center
        //       if true sanity check third token is a treatment MATCH /country/center/treatment
        // 4. tokens = 4
        //

        $tokens = explode('/', substr($uri, 1));
        $tokenCount = count($tokens);

        if (count($tokens) < 2 || count($tokens) > 5) {
            return null;
        }

        // first token should always be a country
        if (is_null($country = $this->doctrine->getRepository('HelperBundle:Country')->findOneBy(array('slug' => $tokens[0])))) {
            return null;
        }

        //MATCHED /country/...
        // second token can either be a center or a city
        //TODO: This assumes that there will be no name collisions between
        // a center and a city.
        if ($center = $this->doctrine->getRepository('MedicalProcedureBundle:MedicalCenter')->findOneBy(array('slug' => $tokens[1]))) {
            if ($tokenCount == 2) {
                // MATCHED /country/center
                $variables = json_encode(array('countryId' => $country->getId(), 'centerId' => $center->getId()));

            } else if ($tokenCount == 3) {
                if ($procedureType = $this->doctrine->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findOneBy(array('slug' => $tokens[2]))) {
                    // MATCHED /country/center/treatment
                    $variables = json_encode(array(
                                    'countryId' => $country->getId(),
                                    'centerId' => $center->getId(),
                                    'procedureTypeId' => $procedureType->getId()
                    ));
                } else {
                    return null;
                }
            } else {
                return null;
            }

        } else if ($city = $this->doctrine->getRepository('HelperBundle:City')->findOneBy(array('slug' => $tokens[1]))) {
            //MATCHED /country/city/...
            // third token should always be a center
            if (is_null($center = $this->doctrine->getRepository('MedicalProcedureBundle:MedicalCenter')->findOneBy(array('slug' => $tokens[2])))) {
                return null;
            }
            if ($tokenCount == 3) {
                //MATCHED /country/city/center
                $variables = json_encode(array(
                                'countryId' => $country->getId(),
                                'cityId' => $city->getId(),
                                'centerId' => $center->getId()
                ));
            } else if ($tokenCount == 4) {
                //MATCHED /country/city/center/...
                // fourth token can either be a treatment or clinic
                //TODO: This assumes that there will be no name collisions between
                // a treatment and a clinic.
                if ($procedureType = $this->doctrine->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findOneBy(array('slug' => $tokens[3]))) {
                    //MATCHED /country/city/center/treatment
                    $variables = json_encode(array(
                                    'countryId' => $country->getId(),
                                    'cityId' => $city->getId(),
                                    'centerId' => $center->getId(),
                                    'procedureTypeId' => $procedureType->getId()
                    ));
                } else if ($clinic = $this->doctrine->getRepository('InstitutionBundle:Institution')->findOneBy(array('slug' => $tokens[3]))) {
                    //MATCHED /country/city/center/clinic
                    $variables = json_encode(array(
                                    'countryId' => $country->getId(),
                                    'cityId' => $city->getId(),
                                    'centerId' => $center->getId(),
                                    'institutionId' => $clinic->getId()
                    ));
                } else {
                    return null;
                }

            } else if ($tokenCount == 5) {
                //MATCHED /country/city/center/...
                //fourth token should be a clinic while the fifth procedure type
                if (is_null($clinic = $this->doctrine->getRepository('InstitutionBundle:Institution')->findOneBy(array('slug' => $tokens[3])))) {
                    return null;
                }
                if (is_null($procedureType = $this->doctrine->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findOneBy(array('slug' => $tokens[4])))) {
                    return null;
                }
                // MATCHED /country/city/center/clinic/treatment
                $variables = json_encode(array(
                                'countryId' => $country->getId(),
                                'cityId' => $city->getId(),
                                'centerId' => $center->getId(),
                                'institutionId' => $clinic->getId(),
                                'procedureTypeId' => $procedureType->getId()
                ));

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