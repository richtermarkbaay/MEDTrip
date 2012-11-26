<?php
namespace HealthCareAbroad\MediaBundle\Controller;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Gaufrette\File;

class InstitutionController extends Controller
{
    public function galleryAction(Request $request)
    {
        $adapter = new ArrayAdapter($this->get('services.media')->retrieveAllMedia($request->get('institutionId'))->toArray());
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 12));

        return $this->render('MediaBundle:Institution:gallery.html.twig', array(
                        'institution' => $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId')),
                        'institutionMedia' => $pager,
                        'routes' => $this->getRoutes($request->getPathInfo()),
                        'context' => $request->get('context')
        ));
    }
}