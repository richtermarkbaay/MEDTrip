<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;

class FeaturedInstitutionAdvertisementFormType extends AdvertisementFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setFormData($options);
        $institution = $this->advertisement->getInstitution();
        $this->buildCommon($builder);
    }
}