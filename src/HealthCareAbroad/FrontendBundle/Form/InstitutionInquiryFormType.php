<?php

namespace HealthCareAbroad\FrontendBundle\Form;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionInquiryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inquirerName', 'text', array('label' => 'Your Name' ))
            ->add('inquirerEmail','email', array('label' => 'Your Email Address'))
            ->add('country','fancy_country', array('label' => 'Your Country'))
            ->add('message', 'textarea', array('label' => 'Enter Your Message'))
            ->add('captcha', 'captcha', array('label'=>'Please type the code'))
            ->add('newsletterSubscription', 'checkbox', array('mapped' => false, 'label' => "Yes, I'd like to subscribe to the HealthcareAbroad newsletter."))
            ;
    }

    public function getName()
    {
        return 'institutionInquiry';
    }
}

