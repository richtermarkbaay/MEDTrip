<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\LogBundle\Entity\VersionEntryActions;

use HealthCareAbroad\LogBundle\Entity\VersionEntry;

use HealthCareAbroad\LogBundle\LogBundle;

use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Query;

use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

use HealthCareAbroad\PagerBundle\Pager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EntityHistoryController extends Controller
{
    private $historyService;
    private $userService;
    private $repositoryObjects = array();
    private $cachedAccountData = array();
    
    public function indexAction(Request $request)
    {
        $filters = array();
        $startDate = new \DateTime($request->get('startDate', null));
        $endDate = new \DateTime($request->get('endDate',null));
        
        if($request->get('startDate')){
            $filters['startDate'] = $startDate->format("Y-m-d H:i:s");
        }
        if($request->get('endDate')){
            $filters['endDate'] = $endDate->format("Y-m-d H:i:s");
        }
        if($request->get('action')){
            $filters['action'] = $request->get('action');
        }
        if($request->get('isClientOnly')){
            $users = $this->getDoctrine()->getRepository('UserBundle:AdminUser')->getAllUsers();
            $ids= array();
            foreach ($users as $user){
                $ids[] = $user['accountId'];
            }
            $filters['isClientOnly'] = $ids;
        }
        
        $qb = $this->getDoctrine()->getRepository('LogBundle:VersionEntry')->getQueryBuilderForFindAll($filters);
        
        $adapter = new DoctrineOrmAdapter($qb, Query::HYDRATE_ARRAY);
        $pager = new Pager($adapter);
        $pager->setLimit(50);
        $pager->setPage($request->get('page', 1));
        
        $this->userService = $this->get('services.twig_user');
        $this->historyService = $this->get('services.log.entity_version');
        
        // build the data into a "more readable" log history
        $entries = array();
        
        foreach ($pager->getResults() as $versionEntry){
            $entries[] = $this->buildViewDataOfVersionEntry($versionEntry);
        }
        $response = $this->render('AdminBundle:EntityHistory:index.html.twig', array(
            'entries' => $entries,
            'pager' => $pager,
            'filter' => $filters,
            'options'=> VersionEntryActions::getActionOptions()
        ));
        
        return $response;
    }
    
    /**
     * Show edit history of an object
     * Required REQUEST parameters are:
     *     objectId - int
     *     objectClass - base64_encoded fully qualified class name
     *
     * @param Request $request
     * @return \HealthCareAbroad\AdminBundle\Controller\Response
     * @author Allejo Chris G. Velarde
     */
    public function showEditHistoryAction(Request $request)
    {
        $objectId = $request->get('objectId', null);
        $objectClass = $request->get('objectClass', null);
        if ($objectId === null || $objectClass === null) {
            return new Response("objectId and objectClass are required parameters", 400);
        }
    
        $objectClass = \base64_decode($objectClass);
        if (!\class_exists($objectClass)) {
            throw $this->createNotFoundException("Cannot view history of invalid class {$objectClass}");
        }
        
        $this->userService = $this->get('services.twig_user');
        $this->historyService = $this->get('services.log.entity_version');
        $filters = array(
            'objectId' => $objectId,
            'objectClass' => $objectClass
        );
        
        $qb = $this->getDoctrine()->getRepository('LogBundle:VersionEntry')
            ->getQueryBuilderForFindAll($filters);
        
        $adapter = new DoctrineOrmAdapter($qb, Query::HYDRATE_ARRAY);
        $pager = new Pager($adapter);
        $pager->setLimit(50);
        $pager->setPage($request->get('page', 1));
        
        foreach ($pager->getResults() as $versionEntry){
            $entries[] = $this->buildViewDataOfVersionEntry($versionEntry);
        }
        
        $response = $this->render('AdminBundle:EntityHistory:editHistory.html.twig', array(
            'entries' => $entries,
            'pager' => $pager,
            'objectId' => $objectId,
            'objectClass' => $objectClass,
        ));
        
        return $response;
        
    }
    
    private function buildViewDataOfVersionEntry($versionEntry)
    {
        
        $entryData = $versionEntry;
        // try to get the object
        $class = $versionEntry['objectClass'];
        if (!isset($this->repositoryObjects[$class])){
            $this->repositoryObjects[$class] = array();
        }
        
        if (!\array_key_exists($versionEntry['objectId'], $this->repositoryObjects[$class])){
            $object = $this->getDoctrine()->getRepository($versionEntry['objectClass'])->find($versionEntry['objectId']);
        
            if ($object){
                $this->repositoryObjects[$class][$versionEntry['objectId']] = $this->historyService->buildGenericViewDataFromObject($object);
            }
            else {
                $this->repositoryObjects[$class][$versionEntry['objectId']] = array(
                    'id' => $versionEntry['objectId'],
                    'name' => null
                );
            }
        }
        $entryData['object'] =  $this->repositoryObjects[$class][$versionEntry['objectId']];
        if (!isset($this->cachedAccountData[$versionEntry['username']])) {
            $accountData = $this->userService->getAccountDataById($versionEntry['username']);
            $accountData['fullName'] = \trim($accountData['first_name'].' '.$accountData['last_name']);
            $this->cachedAccountData[$versionEntry['username']] = $accountData;
        }
        $entryData['user'] = $this->cachedAccountData[$versionEntry['username']];
        
        // build the changed data
        $entryData['data'] = $this->historyService->buildViewDataForChangedData($versionEntry['data']);
        return $entryData;
    }
    
}