<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UnsecuredController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Institution
     */
    private $institution;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId'));
        
        if (!$this->institution) {
            throw $this->createNotFoundException("Invalid institution");
        }
    }
    
    /**
     * Load available global awards of an institution. Used in autocomplete fields
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGlobalAwardSourceAction(Request $request)
    {
//         $start = microtime(true);
//         echo "Start: {$start} ";
        $term = \trim($request->get('term', ''));
        $type = $request->get('type', null);
        $types = \array_flip(GlobalAwardTypes::getTypeKeys());
        $type = \array_key_exists($type, $types) ? $types[$type] : 0;

        $output = array();
        $options = new QueryOptionBag();
        $options->add('globalAward.name', $term);
        if ($type) {
            $options->add('globalAward.type', $type);
        }


        $awards = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')
        ->getAvailableGlobalAwardsOfInstitution($this->institution, $options);


        foreach ($awards as $_award) {
            $output[] = array(
                'id' => $_award->getId(),
                'label' => $_award->getName(),
                'awardingBody' => $_award->getAwardingBody()->getName()
            );
        }
//         $end = microtime(true);
//         echo "End: {$end} ";
//         $diff = $end-$start;
//         echo " {$diff} "; exit;
    
        return new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }
}