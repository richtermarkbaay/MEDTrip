<?php

namespace HealthCareAbroad\UserBundle\Form;

use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionUser = $options['data'];
        
        if (!$institutionUser instanceof InstitutionUser) {
            throw new \Exception('InstitutionUserFormType requires an instance of InstitutionUser as data.');
        }
        
        $builder->add('firstName','text', array('constraints' => new NotBlank()));
        $builder->add('middleName','text', array('constraints' => new NotBlank()));
        $builder->add('lastName','text', array('constraints' => new NotBlank()));
        
        if (!$institutionUser->getAccountId()) {
            //TODO: add constraint for Validating account email as unique
            $builder->add('email','email', array('constraints' => array(
                    new NotBlank(), new Email())
                ))
            ->add( 'password', 'password', array(
                    'label' => 'Password',
                    'virtual' => true,
                    'constraints' => array(new NotBlank())
                ))
            ->add('confirm_password', 'password', array(
                    'label' => 'Confirm Password',
                    'virtual' => true,
                    'constraints' => array(new EqualFieldValue(array('field' => 'password', 'message' => 'Passwords do not match')))
            ));
        }
    
    }
    
    public function getName()
    {
        return 'institutionUserForm';
    }   
}