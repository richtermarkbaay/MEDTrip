<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Doctrine\ORM\Query;

use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

use HealthCareAbroad\PagerBundle\Pager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EntityHistoryController extends Controller
{
    public function indexAction()
    {
        $qb = $this->getDoctrine()->getRepository('LogBundle:VersionEntry')
            ->getQueryBuilderForFindAll();
        
        $adapter = new DoctrineOrmAdapter($qb, Query::HYDRATE_ARRAY);
        $pager = new Pager($adapter);
        $userService = $this->get('services.twig_user');
        $historyService = $this->get('services.log.entity_version');
        
        // build the data into a "more readable" log history
        $entries = array();
        $repositoryObjects = array();
        $cachedAccountData = array();
        foreach ($pager->getResults() as $versionEntry){
            $entryData = $versionEntry;
            // try to get the object
            $class = $versionEntry['objectClass'];
            if (!isset($repositoryObjects[$class])){
                $repositoryObjects[$class] = array();
            }
            
            if (!\array_key_exists($versionEntry['objectId'], $repositoryObjects[$class])){
                $object = $this->getDoctrine()->getRepository($versionEntry['objectClass'])->find($versionEntry['objectId']);
                
                if ($object){
                    $repositoryObjects[$class][$versionEntry['objectId']] = $historyService->buildGenericViewDataFromObject($object);
                }
                else {
                    $repositoryObjects[$class][$versionEntry['objectId']] = array(
                        'id' => $versionEntry['objectId'], 
                        'name' => null
                    );
                }
            }
            $entryData['object'] =  $repositoryObjects[$class][$versionEntry['objectId']];
            if (!isset($cachedAccountData[$versionEntry['username']])) {
                $accountData = $userService->getAccountDataById($versionEntry['username']);
                $accountData['fullName'] = \trim($accountData['first_name'].' '.$accountData['last_name']);
                $cachedAccountData[$versionEntry['username']] = $accountData;
            }
            $entryData['user'] = $cachedAccountData[$versionEntry['username']];
            $entries[] = $entryData;
        }
        
        $response = $this->render('AdminBundle:EntityHistory:index.html.twig', array(
            'entries' => $entries
        ));
        
        return $response;
    }
}