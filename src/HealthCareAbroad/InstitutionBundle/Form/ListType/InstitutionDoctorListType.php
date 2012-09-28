<?php

namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\DoctorTransformer;
use HealthCareAbroad\InstitutionBundle\Entity\Doctor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class InstitutionDoctorListType extends AbstractType
{
    protected  $doctrine;
	
	function setDoctrine($doctrine)
	{
		$this->doctrine = $doctrine;
	}
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new DoctorTransformer($this->doctrine->getEntityManager());
        $builder->prependNormTransformer($transformer);
    }
    
    public function getParent()
    {
        return "text";
    }
    public function getName()
    {
        return 'doctor_list';
    }
}