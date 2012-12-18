<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

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
    function __construct($institution)
    {
        $this->institution = $institution;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                        'is_clientAdmin' => true,
                        'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization'
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionSpecialization = $options['data'];
        
        if (!$institutionSpecialization instanceof InstitutionSpecialization) {
            throw new \Exception('Expected InstitutionSpecialization as data.');
        }

        if ($options['is_clientAdmin']) {
            
            $builder->add('specialization','specializations_autocomplete');
        }else{

            if (!$institutionSpecialization->getId()) {
                 
                $builder->add('specialization', new InstitutionSpecializationListType($this->institution, InstitutionSpecializationListType::SHOW_UNSELECTED), array('virtual'=>false));
            }
            
            $builder->add('description', 'textarea', array(
                            'label' => 'Details',
                            'constraints' => array(new NotBlank()),
                            'attr' => array('class'=>'tinymce')
            ));
        }
    }
    
    public function getName(){
        return 'institutionSpecialization';
    }
}