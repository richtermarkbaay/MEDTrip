<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;

use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;

use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FrontendBundle:Default:index.html.twig', array());
    }

    /**************************************************************************
     Dynamic Routes

     If a name change or a different controller is required modify
     FrontendBundle\Services\FrontendRouteService::extrapolateControllerFromVariables($variables)
     Take note, however, that if this is in production you will have to manually
     change the controller field in the frontend_routes table in the database for
     all entries that have that specific controller.
     **************************************************************************/
    public function commonLandingAction()
    {
        echo 'Route parameters:<br/>';
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function listCentersForCountryAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();

        $country = $em->getRepository('Helper:Country')->find($parameters['countryId']);
        $medicalCenter = $em->getRepository('MedicalProcedure:MedicalCenter')->find($parameters['centerId']);

        $response = new Response();
        $response->headers->setCookie(
            new Cookie('/'.'', $user, 0, '/', null, false, false)
        );

        $institutionMedicalCenters = $em->getRepository('Institution:InstitutionMedicalCenter')
            ->getCentersWithLocation($medicalCenter, $country);
    }

    public function listCentersForCityAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function listTreatmentsForCenterAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function viewTreatmentAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function clinicProfileAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function clinicProfileWithTreatmentAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    /**************************************************************************
     END Dynamic Routes
    **************************************************************************/

    /**
     * Add Error Report
     *
     * @author Chaztine Blance
     */
    public function errorReportAction()
    {

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();

            $errorReport = new ErrorReport();
            $form = $this->createForm(new ErrorReportFormType(), $errorReport);
            $form->bind($request);

            if ($form->isValid()) {

                $errorReport->setLoggedUserId(0);
                $errorReport->setStatus(1);
                $em->persist($errorReport);
                $em->flush($errorReport);

                //// create event on sendEmail and dispatch
                $event = new CreateErrorReportEvent($errorReport);
                $sendResult = $this->get('event_dispatcher')->dispatch(ErrorReportEvent::ON_CREATE_REPORT, $event);

                if ($sendResult) {
                    $this->get('session')->setFlash('success', "Successfully sent error report to HealthCareAbroad");

                }
                else {
                    $this->get('session')->setFlash('error', "Failed to send Report to HealthCareAbroad");
                }
            }

        return $this->render('TwigBundle:Exception:error.html.twig', array(
                        'form' => $form->createView(),
                        'reportSubmitted' => true
        ));
    }
}
