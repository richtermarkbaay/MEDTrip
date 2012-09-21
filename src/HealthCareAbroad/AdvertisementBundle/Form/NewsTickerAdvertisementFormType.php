<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;

class NewsTickerAdvertisementFormType extends AdvertisementFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setFormData($options);
        $this->buildCommon($builder);
    }
}