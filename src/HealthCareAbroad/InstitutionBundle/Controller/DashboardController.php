<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use Symfony\Component\HttpFoundation\Request;

class DashboardController extends InstitutionAwareController
{    
    public function indexAction(Request $request)
    {
        if($request->server->has('HTTP_REFERER')){
            if (\preg_match('/setup-doctors/i', $request->server->get('HTTP_REFERER'))) {
                $newlySignedup = true;
            }
        }

        return $this->render('InstitutionBundle:Dashboard:index.html.twig', array(
            'newlySignedup' => isset($newlySignedup)
        ));
    }    
}
