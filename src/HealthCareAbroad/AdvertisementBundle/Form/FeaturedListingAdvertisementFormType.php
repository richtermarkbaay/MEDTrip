<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\ORM\EntityRepository;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionSpecializationListType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;
use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementMediaFormType;
class FeaturedListingAdvertisementFormType extends AdvertisementFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setFormData($options);
        $institution = $this->advertisement->getInstitution();
        
        $builder->add('object', new InstitutionSpecializationListType(), array(
            'class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter',
            'property' => 'name',
            'query_builder' => function(EntityRepository $er) use($institution) {
                return $er->createQueryBuilder('a')
                    ->select('a')
                    ->where('a.institution = :institutionId AND a.status = :statusActive')
                    ->setParameter('institutionId', $institution->getId())
                    ->setParameter('statusActive', InstitutionMedicalCenterStatus::APPROVED);
            },
            'label' => 'Listing',
            'virtual' => false
        ));
        $builder->add('media', 'file', array('property_path' => false));
        $this->buildCommon($builder);
        
    }
}