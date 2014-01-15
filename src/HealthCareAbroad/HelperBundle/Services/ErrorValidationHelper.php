<?php
namespace HealthCareAbroad\HelperBundle\Services;

use Symfony\Component\Form\Form;

/**
 * 
 * @author allejochrisvelarde
 *
 */
class ErrorValidationHelper
{
    static public function processFormErrorsDeeply(Form $form,array &$errors){
    
        $children = $form->all();
    
        if (!empty($children)){
            foreach ($children as $childForm){
                self::processFormErrorsDeeply($childForm, $errors);
            }
        }
        // get the errors
        $formErrors = $form->getErrors();
        foreach ($formErrors as $err){
            $errors[] = $err->getMessage();
        }
    
        return;
    }
}