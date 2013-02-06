<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\InstitutionBundle\Form\EventSubscriber\InstitutionTreatmentChoiceSubscriber;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionTreatmentChoiceDataTransformer;

use HealthCareAbroad\TreatmentBundle\Form\DataTransformer\TreatmentIdentityTransformer;

use HealthCareAbroad\InstitutionBundle\Form\FieldType\InstitutionTreatmentChoiceType;

use HealthCareAbroad\TreatmentBundle\Form\DataTransformer\SpecializationIdentityTransformer;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionSpecializationListType;
use HealthCareAbroad\InstitutionBundle\Form\ListType\SpecializationListType;
use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\EntityRepository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstitutionSpecializationFormType extends AbstractType
{
    const NAME = 'institutionSpecialization';
    
    private $institutionTreatmentSubscriber;
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'default_choices' => array()
        ));
    }
    
    public function setInstitutionTreatmentEventSubscriber(InstitutionTreatmentChoiceSubscriber $s)
    {
        $this->institutionTreatmentSubscriber = $s;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionSpecialization = $options['data'];
        
        if (!$institutionSpecialization instanceof InstitutionSpecialization) {
            throw new \Exception('Expected InstitutionSpecialization as data.');
        }     
        
        $builder->add($builder->create('treatments', 'choice', array('choices' => $options['default_choices'],'multiple' => true, 'error_bubbling' => true, 'virtual' => true)));
    }
    
    public function getName(){
        return self::NAME;
    }
}