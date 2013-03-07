<?php
namespace HealthCareAbroad\HelperBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class TestController extends Controller
{
    public function showFormAction()
    {
        $form = $this->createForm(new TrackerFormType());

        return  $this->render('StatisticsBundle:Tracker:form.html.twig', array('statsTrackerForm' => $form->createView()));
    }

    public function baselineAction(Request $request)
    {
        return $this->render('HelperBundle:Test:baseline.html.twig');
    }

    public function baselineResourcesAction(Request $request)
    {
        return $this->render('HelperBundle:Test:baselineResources.html.twig');
    }

    public function frontendBaseAction(Request $request)
    {
        return $this->render('HelperBundle:Test:frontendBase.html.twig');
    }

    public function saveClickthroughAction(Request $request)
    {
        $this->_throwUnsupportedMethodIfNotPost($request);

        $form = $this->createForm(new TrackerFormType());
        $form->bind($request);

        // invalid form. invalid token
        if (!$form->isValid()) {
            $this->_throwHttpException(400, 'Invalid form');
        }

        $clickthroughData = \trim($request->get('clickthroughData', ''));
        if ('' == $clickthroughData) {
            $this->_throwHttpException(400, 'no click through data');
        }
        $trackerFactory = $this->get('factory.statistics.dailyTracker');
        $decodedData = StatisticParameters::decodeParameters($clickthroughData);
        $tracker = $trackerFactory->getTrackerByType($decodedData->get(StatisticParameters::TYPE));
        if ($data = $tracker->createDataFromParameters($decodedData)) {
            $tracker->add($data);
            $tracker->batchSave();
        }

        return new Response('Clickthrough ok', 200);
    }
}