<?php
namespace HealthCareAbroad\ProviderBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

class ProviderUserInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email')
            ->add('message', 'textarea')
            ->add('firstName', 'text')
            ->add('middleName', 'text')
            ->add('lastName', 'text');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation',
        ));
    }
    
    public function getName()
    {
        return 'providerUserInvitation';
    }
}