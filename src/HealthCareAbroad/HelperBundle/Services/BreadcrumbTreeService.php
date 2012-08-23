<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Repository\BreadcrumbTreeRepository;

use DoctrineExtensions\NestedSet\Manager;

use DoctrineExtensions\NestedSet\Config;

use HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree;

use Doctrine\Bundle\DoctrineBundle\Registry;

class BreadcrumbTreeService
{
    /**
     * @var Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
    
    /**
     * @var DoctrineExtensions\NestedSet\Config
     */
    private $nsmConfig;
    
    /**
     * @var DoctrineExtensions\NestedSet\Manager
     */
    private $nsmManager;
    
    /**
     * @var HealthCareAbroad\HelperBundle\Repository\BreadcrumbTreeRepository
     */
    private $repository = null;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('HelperBundle:BreadcrumbTree');
        $this->nsmConfig = new Config($this->doctrine->getEntityManager(), 'HelperBundle:BreadcrumbTree');
        $this->nsmConfig->setLeftFieldName('leftValue');
        $this->nsmConfig->setRightFieldName('rightValue');
        $this->nsmConfig->setRootFieldName('rootId');
        
        $this->nsmManager = new Manager($this->nsmConfig);
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function getNode($id)
    {
        $node = $this->repository->find($id);
        if (!$node) {
            return null;
        }
        
        return $this->nsmManager->wrapNode($node);
    }
    
    public function getAllNodesOfTree(BreadcrumbTree $root)
    {
        $nodes = $this->repository->createQueryBuilder('a')
            ->orderBy('a.leftValue','asc')
            ->getQuery()
            ->getResult();
        $wrappedNodes = array();
        foreach ($nodes as $e) {
            $wrappedNodes[] = $this->nsmManager->wrapNode($e);
        }
        return $wrappedNodes;
    }
    
    public function getLeafNodesOfTree(BreadcrumbTree $root)
    {
        $nodes = $this->repository->createQueryBuilder('a')
            ->where('a.rightValue = a.leftValue+1')
            ->getQuery()
            ->getResult();
        $wrappedNodes = array();
        foreach ($nodes as $e) {
            $wrappedNodes[] = $this->nsmManager->wrapNode($e);
        }
        return $wrappedNodes;
    }
    
    public function addChild(BreadcrumbTree $parent, BreadcrumbTree $node)
    {
        $parentNode = $this->nsmManager->wrapNode($parent);
        $parentNode->addChild($node);
    }
    
    public function fetchTree(BreadcrumbTree $crumb)
    {
        return $this->nsmManager->fetchTree($crumb->getId());
    }
    
    public function createRoot(BreadcrumbTree $crumb)
    {
        $this->nsmManager->createRoot($crumb);
    }
}