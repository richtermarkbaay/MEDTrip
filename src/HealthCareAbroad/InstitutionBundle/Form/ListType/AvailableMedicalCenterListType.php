<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class AvailableMedicalCenterListType extends AbstractType
{
    /**
     * 
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    public function __construct(Institution $institution) {
        $this->institution = $institution;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $institution = $this->institution;   
        $resolver->setDefaults(array(
            'empty_value' => '<-- select center -->',
            'property' => 'name',
            'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter',
            'query_builder' => function (EntityRepository $er) use ($institution) {
                    return $er->getQueryBuilderForUnselectedInstitutionMedicalCenters($institution);
                }
        ));
    }
     
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'institutionMedicalCenter_list';
    }
}