<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\EntityRepository;
use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionAffiliationListType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionAffiliationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('institutionAffiliations', new InstitutionAffiliationListType(), array('expanded' => true,'multiple' => true));
    }
    
    public function getName(){
        return 'institutionAffiliation';
    }
}