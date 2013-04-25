<?php
/**
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\HelperBundle\Validator\Constraints\CorrectCurrentPassword;

use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

use \Exception;

class InstitutionUserResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $siteUser = $options['data'];
        
        if (!$siteUser instanceof SiteUser) {
            throw new \Exception(__CLASS__.' expects a HealthCareAbroad\UserBundle\Entity\SiteUser instance as data.');
        }
        
        if (!$siteUser->getAccountId()) {
            throw new \Exception(__CLASS__.' expects a HealthCareAbroad\UserBundle\Entity\SiteUser instance with valid accountId as data');
        }
        
        $builder
    	    ->add( 'new_password', 'password', array(
                    'label' => 'New Password', 
                    'virtual' => true, 
                    'constraints' => array(new NotBlank())
                ))
    	    ->add('confirm_password', 'password', array(
                    'label' => 'Confirm Password', 
                    'virtual' => true, 
                    'constraints' => array(
                        new EqualFieldValue(array('field' => 'new_password', 'message' => 'Passwords do not match')))
                ));
    }
    
    public function getName()
    {
        return 'institutionUserResetPasswordType';
    }
}