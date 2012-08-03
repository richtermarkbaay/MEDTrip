<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

class InstitutionUserInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('constraints' => array( new Email(), new NotBlank())))
            ->add('message', 'textarea', array('constraints' => array(new NotBlank(array('message' => 'Message is required.')))))
            ->add('firstName', 'text', array('constraints' => array(new NotBlank())))
            ->add('middleName', 'text', array('constraints' => array(new NotBlank())))
            ->add('lastName', 'text', array('constraints' => array(new NotBlank())));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation',
        ));
    }
    
    public function getName()
    {
        return 'institutionUserInvitation';
    }
}