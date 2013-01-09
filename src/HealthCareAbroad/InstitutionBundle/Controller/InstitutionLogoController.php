<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\MediaBundle\Services\MediaService;

use HealthCareAbroad\MediaBundle\Entity\Gallery;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Gaufrette\File;

class InstitutionLogoController extends InstitutionAwareController
{
    //TODO: refactor
    public function uploadAction(Request $request)
    {
        $response = new Response();

        $fileBag = $request->files;
        if ($fileBag->has('file')) {

            $result = $this->get('services.media')->upload($fileBag->get('file'), $this->institution);
            
            
            if(is_object($result)) {
         
                $media = $result;
                    $this->get('services.institution')->saveMediaAsLogo($this->institution, $media);
                    return $this->redirect($this->generateUrl('institution_homepage'));
            }
            return $response;
            
        } else {

            return $response->create('File not detected', 415);
        }
    }
}
