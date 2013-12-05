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
        
        foreach ($listFilter->getServiceDependencies() as $serviceId) {
            $listFilter->injectDependency($serviceId, $controller[0]->get($serviceId));
        }
        
        $params = array_merge($request->get('_route_params'), $request->query->all());

        if($listFilter) {
            
            // client admin filter
            if (\preg_match('/^$routeName/', $routeName)) {
                $params['institutionId'] = $request->getSession()->get('institutionId', 0);
                $params['isInstitutionContext'] = true;
            }

            $listFilter->prepare($params);

            $controller[0]->pager = $listFilter->getPager();
            $controller[0]->filteredResult = $listFilter->getFilteredResult();

            $listFilters = $this->twig->render('HelperBundle:Default:filters.html.twig', array(
                'filters' => $listFilter->getFilterOptions(),
                'url' => $this->router->generate($routeName, $request->get('_route_params'))
            ));

            $this->twig->addGlobal('listFilters', $listFilters);

            $this->twig->addGlobal('pager', $controller[0]->pager);
        }
    }
}