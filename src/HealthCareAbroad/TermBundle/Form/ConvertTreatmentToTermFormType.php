<?php

namespace HealthCareAbroad\TermBundle\Form;

use HealthCareAbroad\HelperBundle\Entity\City;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
class ConvertTreatmentToTermFormType extends AbstractType
{

    protected $treatmentChoices = array();
    
    public function setTreatmentChoices(array $treatments)
    {
        //$this->treatmentChoices = $treatments;
//         $treatmentChoices = array();
        foreach ($treatments as $each) {
            $this->treatmentChoices[$each->getId()] = $each->getName();
        }
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//         var_dump($this->treatmentChoices);exit;
        $builder->add($builder->create('treatments', 'choice', array('label'=> 'Convert as Tag of Treatment', 'choices' => $this->treatmentChoices)));
    }
    
    public function getName()
    {
        return 'convert_treatment_to_term_form';
    }
    
}