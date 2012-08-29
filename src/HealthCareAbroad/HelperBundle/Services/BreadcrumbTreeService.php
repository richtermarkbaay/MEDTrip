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
     * @var HealthCareAbroad\HelperBundle\Repository\BreadcrumbTreeRepository
     */
    private $repository = null;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('HelperBundle:BreadcrumbTree');
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function getPathOfNode(BreadcrumbTree $node, $includeSelf=true)
    {
        $path = $this->repository->getPath($node);
        if (!$includeSelf) {
            unset($path[count($path)-1]);    
        }
        
        return $path;
    }
    
    /**
     * Find a breadcrumbtree node by route
     * 
     * @param string $route
     * @return BreadcrumbTree
     */
    public function getNodeByRoute($route)
    {
        $node = $this->repository->findOneBy(array('route' => $route));
        if (!$node) {
            return null;
        }
        
        return $node;
    }
    
    /**
     * Find a breadcrumbtree node by primary key
     * 
     * @param int $id
     * @return BreadcrumbTree
     */
    public function getNode($id)
    {
        $node = $this->repository->find($id);
        if (!$node) {
            return null;
        }
        
        return $node;
    }
    
    public function getAllNodesOfTree(BreadcrumbTree $root)
    {
        $nodes = $this->repository->createQueryBuilder('a')
            ->where('a.rootId = :rootId')
            ->orderBy('a.leftValue','asc')
            ->setParameter('rootId', $root->getId())
            ->getQuery()
            ->getResult();
        
        return $nodes;
    }
    
    public function getLeafNodesOfTree(BreadcrumbTree $root)
    {
        $nodes = $this->repository->createQueryBuilder('a')
            ->where('a.rightValue = a.leftValue+1')
            ->getQuery()
            ->getResult();
        
        return $nodes;
    }
}