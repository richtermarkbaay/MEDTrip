<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

use Doctrine\ORM\EntityRepository;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionMedicalCenterListType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;

class FeaturedListingAdvertisementFormType extends AdvertisementFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setFormData($options);
        $institution = $this->advertisement->getInstitution();
        $builder->add('object', new InstitutionMedicalCenterListType(), array(
            'class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter',
            'property' => 'medicalCenter',
            'query_builder' => function(EntityRepository $er) use($institution) {
                return $er->createQueryBuilder('a')
                    ->select('a')
                    ->where('a.institution = :institutionId AND a.status = :statusActive')
                    ->setParameter('institutionId', $institution->getId())
                    ->setParameter('statusActive', InstitutionMedicalCenterGroupStatus::APPROVED);
            },
            'label' => 'Listing',
            'virtual' => false
        ));
        
        $this->buildCommon($builder);
    }
}