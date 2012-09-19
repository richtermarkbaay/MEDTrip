<?php

/**
 * @author Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Listener;


use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use HealthCareAbroad\HelperBundle\Services\Filters\ListFilterFactory;


class ListFilterBeforeController
{
    private $twig;

    private $doctrine;

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function setRouter($router)
    {
        $this->router = $router;
    }

    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * kernel.controller listener method
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        $routeName = $request->get('_route');

        if(!ListFilterFactory::isValidRouteName($routeName))
            return;

        $controller[0]->filteredResult = array();

        $listFilter = ListFilterFactory::create($routeName, $this->doctrine);
        $params = array_merge($request->get('_route_params'), $request->query->all());

        if($listFilter) {

            if($request->getSession()->get('institutionId')) {
                $params['institutionId'] = $request->getSession()->get('institutionId');
                $params['isInstitutionContext'] = true;
            }

            $listFilter->prepare($params);

            $controller[0]->filteredResult = $listFilter->getFilteredResult();
            $controller[0]->pager = $listFilter->getPager();

            $urlParams = $request->get('_route_params');

            if(isset($params['limit']))
                $urlParams['limit'] = $params['limit'];

            $listFilters = $this->twig->render('HelperBundle:Default:filters.html.twig', array(
                'filters' => $listFilter->getFilterOptions(),
                'url' => $this->router->generate($routeName, $urlParams)
            ));

            $this->twig->addGlobal('listFilters', $listFilters);

            $this->twig->addGlobal('pager', $listFilters);
        }
    }
}