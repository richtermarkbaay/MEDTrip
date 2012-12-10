<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\EntityRepository;
use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionOfferedServiceListType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class InstitutionOfferedServicesFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    	$builder->add('institutionOfferedServices', new InstitutionOfferedServiceListType(), array('expanded' => true,'multiple' => true));
    }
    
    public function getName(){
        return 'admin';
    }
}