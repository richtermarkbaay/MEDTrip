<?php

/**
 * @author Alnie Jacobe
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\PayingStatus;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserFormType;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\AdminBundle\Entity\OfferedService;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionOfferedServiceType;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionController extends InstitutionAwareController
{

	public function viewAllStaffAction(Request $request)
	{
	    
	    $users = $this->get('services.institution')->getAllStaffOfInstitution($this->institution);
	    $userTypes = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->getAllEditable($this->institution);

	    $institutionUser = new InstitutionUser();
	    $form = $this->createForm(new InstitutionUserFormType(), $institutionUser);

	    return $this->render('InstitutionBundle:InstitutionUser:viewAll.html.twig', array(
            'users' => $users,
            'userTypes' => $userTypes,
            'form' => $form->createView()
	    ));
	}
	
	public function uploadLogoAction()
	{
	    $data = array('status' => false);
	    if($this->getRequest()->files->get('logo')) {
	        $file = $this->getRequest()->files->get('logo');
	        $media = $this->get('services.institution.media')->uploadLogo($file, $this->institution);
	        if($media->getName()) {
	            $imageSize = ImageSizes::MEDIUM;
	            if($this->getRequest()->get('logoSize') == ImageSizes::SMALL) {
	                $imageSize = ImageSizes::SMALL;
	            }
	            $src = $this->get('services.institution')->mediaTwigExtension->getInstitutionMediaSrc($media->getName(), $imageSize);
	            $data['mediaSrc'] = $src;

	            // Invalidate InstitutionProfile memcache
	            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));
	        }
	        $data['status'] = true;
	    }

        return new Response(\json_encode($data), 200, array('content-type' => 'application/json')); 
	}
	
	public function uploadFeaturedImageAction()
	{
	    $data = array('status' => false);
	    if($this->getRequest()->files->get('featuredImage')) {
	        $file = $this->getRequest()->files->get('featuredImage');
	        $media = $this->get('services.institution.media')->uploadFeaturedImage($file, $this->institution);
	        if($media->getName()) {
	            $imageSize = ImageSizes::LARGE_BANNER;
	            
	            if($this->getRequest()->get('logoSize') == ImageSizes::SMALL) {
	                $imageSize = ImageSizes::SMALL;
	            }
	            $src = $this->get('services.institution')->mediaTwigExtension->getInstitutionMediaSrc($media->getName(), $imageSize);
	            $data['mediaSrc'] = $src;

	            // Invalidate InstitutionProfile memcache
	            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));
	            
	            // Check if institution is paying client 
	            if($this->institution->getPayingClient()) {

	                // Invalidate all InstitutionMedicalCenterProfile memcache
	                foreach($this->institution->getInstitutionMedicalCenters() as $each) {
	                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($each->getId()));
	                }
	            }
	        }
	        $data['status'] = true;
	    }
	    
	    return new Response(\json_encode($data), 200, array('content-type' => 'application/json'));
	}
}