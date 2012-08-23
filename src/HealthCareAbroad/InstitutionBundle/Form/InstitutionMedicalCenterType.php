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
		$status = array(
			InstitutionMedicalCenter::STATUS_ACTIVE => 'active',
			InstitutionMedicalCenter::STATUS_INACTIVE => 'inactive'
		);

        // we are expecting only an InstitutionMedicalCenter as data
        $institutionMedicalCenter = $options['data'];
        $institution = $options['institution'];
        $institutionMedicalCenter->setInstitution($institution);

        if (!$medicalCenterId = $institutionMedicalCenter->getMedicalCenterId()) {
            $builder->add('medicalCenter', 'medicalCenter_list', array('query_builder' => function (EntityRepository $er) use ($institution) {
                    return $er->getQueryBuilderForUnselectedInstitutionMedicalCenters($institution);
                },'virtual' => false,'empty_value' => 'Please select one','constraints'=>array(new NotBlank())));
        }
        else {
        	 
            $builder->add('medicalCenter', 'medicalCenter_list', array('query_builder' => function(EntityRepository $er) use($medicalCenterId){
                return $er->createQueryBuilder('a')
                    ->where('a.id = :id')
                    ->setParameter('id', $medicalCenterId);
            }, 'virtual' => false,'constraints'=>array(new NotBlank())));
        }
        
        $builder->add('description', 'textarea', array('constraints' => new NotBlank()));
        $builder->add('status', 'choice', array('choices' => $status));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('institution'));
        $resolver->setOptional(array('medicalCenterId'));
        $resolver->setAllowedTypes(array('institution' => 'HealthCareAbroad\InstitutionBundle\Entity\Institution'));
    }
    
    public function getName()
    {
        return 'institutionMedicalCenter';
    }
}