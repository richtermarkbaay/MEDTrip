<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\EntityRepository;
use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionGlobalAwardListType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

// class InstitutionGlobalAwardFormType extends AbstractType
// {

//     public function buildForm(FormBuilderInterface $builder, array $options)
//     {

//         $builder->add('institutionGlobalAwards', new InstitutionGlobalAwardListType(), array('expanded' => true,'multiple' => true));
//     }
    
//     public function getName(){
//         return 'institutionGlobalAward';
//     }
// }