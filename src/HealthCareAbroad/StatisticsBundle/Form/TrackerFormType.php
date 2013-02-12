<?php

namespace HealthCareAbroad\StatisticsBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class TrackerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    }
    
    public function getName()
    {
        return 'hca_statistics_tracker_form';
    }
}