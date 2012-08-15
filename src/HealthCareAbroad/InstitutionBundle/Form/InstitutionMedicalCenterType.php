<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\InstitutionBundle\Form\ListType\AvailableMedicalCenterListType;

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
        
        if (!$institutionMedicalCenter instanceof InstitutionMedicalCenter) {
            throw new \Exception("Expecting an instance of HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter as data. ".\get_class($institutionMedicalCenter)." given.");
        }
        
        $builder->add('medicalCenter', new AvailableMedicalCenterListType($institutionMedicalCenter->getInstitution()), array('empty_value' => 'Please select one','constraints'=>array(new NotBlank())));
        $builder->add('description', 'textarea', array('constraints' => new NotBlank()));
        $builder->add('status', 'choice', array('choices' => $status));
    }
    
    public function getName()
    {
        return 'institutionMedicalCenter';
    }
}