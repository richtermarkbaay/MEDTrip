<?php

namespace HealthCareAbroad\StatisticsBundle\Controller;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\StatisticsBundle\Form\TrackerFormType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrackerController extends Controller
{
//     public function showFormAction()
//     {
//         $form = $this->createForm(new TrackerFormType());
        
//         return  $this->render('StatisticsBundle:Tracker:form.html.twig', array('statsTrackerForm' => $form->createView()));
//     }
    
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
    
    public function saveImpressionsAction(Request $request)
    {
        $this->_throwUnsupportedMethodIfNotPost($request);
        
        $form = $this->createForm(new TrackerFormType());
        $form->bind($request);
        
        // invalid form
        if (!$form->isValid()) {
            $this->_throwHttpException(400, 'Invalid form');
        }
        
        $impressions = $request->get('impressions', array());
        if (!\count($impressions)) {
            return new Response('Empty impressions', 200);
            //$this->_throwHttpException(400, 'Empty impressions');
        }
        
        $trackerFactory = $this->get('factory.statistics.dailyTracker');
        $trackersUsed = array();
        
        // add impressions to daily trackers
        foreach ($impressions as $_encodedImpression){
            $decodedImpression = StatisticParameters::decodeParameters($_encodedImpression);
            
            // no statisic type parameter, or invalid type
            if (!$decodedImpression->has(StatisticParameters::TYPE) || !StatisticTypes::isValidType($decodedImpression->get(StatisticParameters::TYPE))) {
                // lets skip this for now. 
                continue;
            }
            
            if (!isset($trackersUsed[$decodedImpression->get(StatisticParameters::TYPE)])) {
                $trackersUsed[$decodedImpression->get(StatisticParameters::TYPE)] = $trackerFactory->getTrackerByType($decodedImpression->get(StatisticParameters::TYPE)); 
            }
            $tracker = $trackersUsed[$decodedImpression->get(StatisticParameters::TYPE)]; 
            
            if ($data = $tracker->createDataFromParameters($decodedImpression)){
                
//                 $em = $this->getDoctrine()->getEntityManager('statistics');
//                 $em->persist($data);
//                 $em->flush();
                
//                 return $this->render('::base.ajaxDebugger.html.twig');
//                 var_dump($data); exit;
                
                $tracker->add($data);
            }
        }
        
        // batch save the impressions
        foreach ($trackersUsed as $tracker) {
            try {
                $tracker->batchSave();
            }
            catch(\Exception $e) {
                // log this exception
                
            }
        }
        
        return new Response('Impressions saved', 200);
    }
    
    private function _throwUnsupportedMethodIfNotPost(Request $request)
    {
        if (!$request->isMethod('POST')) {
            $this->_throwHttpException(405, 'Unsupported method '.$request->getMethod()); 
        }
    }
    
    private function _throwHttpException($errorCode, $message)
    {
        throw new HttpException($errorCode, $message);
    }
}