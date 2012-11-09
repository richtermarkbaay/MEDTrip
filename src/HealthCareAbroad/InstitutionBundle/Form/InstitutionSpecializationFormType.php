<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionSpecializationListType;

use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\EntityRepository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionSpecializationFormType extends AbstractType
{
    function __construct($institution)
    {
        $this->institution = $institution;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionSpecialization = $options['data'];
        
        if (!$institutionSpecialization instanceof InstitutionSpecialization) {
            throw new \Exception('Expected InstitutionSpecialization as data.');
        }

        if (!$institutionSpecialization->getId()) {
            $builder->add('specialization', new InstitutionSpecializationListType($this->institution), array('virtual'=>false));
        }
        
//         $builder->add('treatment', 'entity', array(
//             'label' => 'Treatments',
//             'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Treatment',
//             'multiple' => true,
//             'attr' => array('class' => 'institutionTreatments')
//         ));

        $builder->add('description', 'textarea', array(
            'label' => 'Details',
            'constraints' => array(new NotBlank()),
            'attr' => array('class'=>'tinymce')
        ));
    }
    
    public function getName(){
        return 'institutionSpecialization';
    }
}