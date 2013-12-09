<?php
/**
 * @author adelbertsilla
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class StatisticsController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_STATISTICS')")
     */
    public function institutionsAction()
    {
        return $this->render('AdminBundle:Statistics:institutions.html.twig', array(
            'statsData' => $this->filteredResult,
            'categories' => StatisticCategories::getInstitutionCategories()
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_STATISTICS')")
     */
    public function institutionMedicalCentersAction()
    {
        return $this->render('AdminBundle:Statistics:institutionMedicalCenters.html.twig', array(
            'statsData' => $this->filteredResult,
            'categories' => StatisticCategories::getInstitutionMedicalCenterCategories()
        ));
    }
}