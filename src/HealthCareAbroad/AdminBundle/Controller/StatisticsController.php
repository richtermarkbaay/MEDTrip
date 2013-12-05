<?php
/**
 * @author adelbertsilla
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use Doctrine\Common\Util\Inflector;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\FormError;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\State;
use HealthCareAbroad\HelperBundle\Form\StateFormType;
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
            'pager' => $this->pager
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_STATISTICS')")
     */
    public function institutionMedicalCentersAction()
    {
//         return $this->render('AdminBundle:Statistics:institutionMedicalCenters.html.twig', array(
//             'statsData' => $this->filteredResult,
//             'pager' => $this->pager
//         ));
    }
}