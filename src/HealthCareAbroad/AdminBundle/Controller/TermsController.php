<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TermsController extends Controller
{
    public function indexAction()
    {
        $terms = $this->getDoctrine()->getEntityManager()->getRepository('TermBundle:Term')->findAll();
        $data = array('terms'=>$terms);
        return $this->render('AdminBundle:Terms:index.html.twig', $data);
    }
    
    public function loadAutocompleteSourceAction(Request $request)
    {
        $type = $request->get('type', 3);
        $term = \trim($request->get('term', ''));
        $terms = array();
        
        if ('' != $term) {
            $result = $this->get('services.terms')->findByName($term, 20);
            foreach ($result as $_each) {
                $html = '<span data-termId="'.$_each->getId().'" style="border: 1px inset; padding: 3px 5px; margin-right: 3px; cursor: pointer;" class="autocompleteSelected">'.$_each->getName().'</span>';
                $terms[] = array(
                    'id' => $_each->getId(),
                    //'value' => $_each->getId(),
                    'label' => $_each->getName(),
                    'html' => $html
                );
            }
        }
        
        return new Response(\json_encode($terms), 200, array('content-type' => 'application/json'));
    }
       
}