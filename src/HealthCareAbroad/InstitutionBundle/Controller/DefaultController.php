<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

class DefaultController extends InstitutionAwareController
{
    public function indexAction()
    {
        return $this->render('InstitutionBundle:Default:index.html.twig');
    }
}
