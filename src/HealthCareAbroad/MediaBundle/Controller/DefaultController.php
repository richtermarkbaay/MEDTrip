<?php
namespace HealthCareAbroad\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    public function editCaptionAction(Request $request)
    {
        $media = $this->getDoctrine()->getRepository('MediaBundle:Media')->find($request->get('id'));
        
        if($media) {
            $media->setCaption($request->get('caption'));

            $em = $this->getDoctrine()->getEntityManagerForClass('MediaBundle:Media');
            $em->persist($media);
            $em->flush();
        }

        $response = 0;
        if ($media) {
            $response = $media->getId();
        }

        return new Response($response);
    }
}