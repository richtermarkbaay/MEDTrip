<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TermsController extends Controller
{
    public function loadAutocompleteSourceAction(Request $request)
    {
        $type = $request->get('type', 3);
        $terms = array();
        for ($x=1; $x<=3; $x++){
            $label = "Test{$x}";
            $html = '<span style="border: 1px inset; padding: 3px 5px; margin-right: 3px; cursor: pointer;" class="autocompleteSelected">'.$label.'</span>';
            $terms[] = array(
                'id' => $x,
                'label' => $label,
                'html' => $html
            );
        }
        
        return new Response(\json_encode($terms), 200, array('content-type' => 'application/json'));
    }
       
}