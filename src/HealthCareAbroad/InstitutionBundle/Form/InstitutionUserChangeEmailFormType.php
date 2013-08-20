<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\HelperBundle\Validator\Constraints\ValidAccountEmail;
use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormViewInterface;

use Doctrine\ORM\EntityRepository;

/**
 * @author Chaztine Blance
 */
class InstitutionUserChangeEmailFormType extends AbstractType
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $siteUser = $options['data'];
        
        if (!$siteUser instanceof SiteUser) {
            throw new \Exception(__CLASS__.' expects a HealthCareAbroad\UserBundle\Entity\SiteUser instance as data.');
        }
        
        if (!$siteUser->getAccountId()) {
            throw new \Exception(__CLASS__.' expects a HealthCareAbroad\UserBundle\Entity\SiteUser instance with valid accountId as data');
        }
        
        $institutionUser = $options['data'];
        if (!$institutionUser instanceof InstitutionUser) {
            throw new \Exception('Institution User sign up form expects an InstitutionUser for data');
        }
        
        $builder
        ->add( 'new_email', 'email', array(
                        'label' => 'New Email',
                        'virtual' => true,
                        'constraints' => array(new NotBlank(array('message'=>'Email address is required.')) ,new ValidAccountEmail(array('currentAccountEmail' => $institutionUser->getEmail(), 'field' => 'email')))
        ))
        ->add('confirm_email', 'email', array(
                        'label' => 'Confirm Email',
                        'virtual' => true,
                        'constraints' => array(new EqualFieldValue(array('field' => 'new_email', 'message' => 'Email address do not match')))
        ));
    }
    
    public function getName()
    {
        return 'institutionUserChangeEmail';
    }
}