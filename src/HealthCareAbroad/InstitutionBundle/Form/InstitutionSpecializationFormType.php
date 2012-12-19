<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

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
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'em' => null,
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization'
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionSpecialization = $options['data'];
        
        if (!$institutionSpecialization instanceof InstitutionSpecialization) {
            throw new \Exception('Expected InstitutionSpecialization as data.');
        }        
            
        /**$builder->add('description', 'textarea', array(
            'label' => 'Details',
            'constraints' => array(new NotBlank())
        ));**/

        $builder->add($builder->create('specialization', 'hidden', array('error_bubbling' => true))
            ->addModelTransformer(new SpecializationIdentityTransformer($options['em']))
        );
        
        $builder->add($builder->create('treatments', new InstitutionTreatmentChoiceType(), array('error_bubbling' => true, 'virtual' => false))
            //->addModelTransformer(new TreatmentIdentityTransformer($options['em']))
        );
    }
    
    public function getName(){
        return self::NAME;
    }
}