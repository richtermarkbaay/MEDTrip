<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Symfony\Component\Form\AbstractType;

class AjaxLoadedOptionListType extends AbstractType
{
    public function getParent()
    {
        return 'field';
    }
}