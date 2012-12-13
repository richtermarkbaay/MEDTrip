<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\EntityRepository;
use HealthCareAbroad\HelperBundle\Form\ListType\LanguageListType;
use HealthCareAbroad\InstitutionBundle\Form\Transformer\LanguageTransformer;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class InstitutionLanguageSpokenFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('institutionLanguagesSpoken','language_autocomplete', array('constraints' => new NotBlank(),'label' => ' '));
    }
    
    public function getName(){
        return 'admin';
    }
}