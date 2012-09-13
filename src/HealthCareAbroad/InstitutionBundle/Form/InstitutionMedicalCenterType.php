<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 * @author Adelber Silla
 */

namespace HealthCareAbroad\InstitutionBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionMedicalCenterListType;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Component\Form\AbstractType;

class InstitutionMedicalCenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		// we are expecting only an InstitutionMedicalCenter as data
        $institutionMedicalCenter = $options['data'];

        if (!$institutionMedicalCenter->getId()) {
            $institution = $institutionMedicalCenter->getInstitution();
            $builder->add('medicalCenter', 'medicalCenter_list', array('query_builder' => function (EntityRepository $er) use ($institution) {
                    return $er->getQueryBuilderForUnselectedInstitutionMedicalCenters($institution);
                },'virtual' => false,'empty_value' => 'Please select one','constraints'=>array(new NotBlank())));
        }
        else {
            $medicalCenterId = $institutionMedicalCenter->getMedicalCenter()->getId(); 
            $builder->add('medicalCenter', 'medicalCenter_list', array('query_builder' => function(EntityRepository $er) use($medicalCenterId){
                return $er->createQueryBuilder('a')
                    ->where('a.id = :id')
                    ->setParameter('id', $medicalCenterId);
            }, 'virtual' => false,'constraints'=>array(new NotBlank())));
        }
        
        $builder->add('description', 'textarea', array('constraints' => new NotBlank(), 'attr' => array('class' => 'tinymce')));
    }
    
    public function getName()
    {
        return 'institutionMedicalCenter';
    }
}