<?php 
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionUserTypeController extends Controller 
{
    /**
     * View all user types
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function indexAction()
    {
    	echo "teest";
    	echo $institutionId;exit;
    	$institutionId = $this->getRequest()->get('institutionId', null);
    	
    	if (!$institutionId){
    		// no account id in parameter, editing currently logged in account
    		$session = $this->getRequest()->getSession();
    		$institutionId = $session->get('institutionId');
    	}
    	echo $institutionId;exit;
        $userTypes = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->getUserTypesByInstitutionId($institutionId);
        return $this->render('InstitutionBundle:InstitutionUserType:index.html.twig', array(
            'userTypes' => $userTypes
        ));
    }
}