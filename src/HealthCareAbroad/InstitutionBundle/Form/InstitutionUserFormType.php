<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\HelperBundle\Validator\Constraints\ValidAccountEmail;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Form\ListType\MedicalProviderGroupListType;
use HealthCareAbroad\InstitutionBundle\Form\Transformer\MedicalProviderGroupTransformer;
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
 * Form used in add InstitutionUser page. 
 * 
 * @author Al
 */
class InstitutionUserFormType extends AbstractType
{
    
    private $container;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $institutionUser = $options['data'];
        if (!$institutionUser instanceof InstitutionUser) {
            throw new \Exception('Institution User add form expects an InstitutionUser for data');
        }
        
        $builder
        ->add('email', 'email', array( 'error_bubbling' => false, 'constraints' => array(new ValidAccountEmail(array('currentAccountEmail' => $institutionUser->getEmail(), 'field' => 'email')), new NotBlank(array('message' => 'Please provide your email address. ')))))
        ->add('password', 'password', array('label' => 'Password','error_bubbling' => false,'constraints' => array(new NotBlank(array('message'=>'Password is required.')))))
        ->add('confirm_password', 'password', array('label' => 'Re-type password','virtual' => true,'error_bubbling' => false,'constraints' => array(new EqualFieldValue(array('field' => 'password', 'message' => 'Passwords do not match')))))
        
        ;
        
    }
    
    public function getName()
    {
        return 'institutionUser';
    }
}