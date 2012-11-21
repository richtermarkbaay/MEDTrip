<?php
/**
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use HealthCareAbroad\MemcacheBundle\Services\NamespacePrefixStorage;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionRepository;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionFactoryException;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Factory class for Institution. Accessible by services.institution.factory service key
 * 
 */
class InstitutionFactory
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var InstitutionRepository
     */
    private $repository;
    
    private $discriminatorMapping = array();
    
    /**
     * @var NamespacePrefixStorage
     */
    private $memcacheNamespacePrefixStorage;
    
    /**
     * @var MemcacheService
     */
    private $memcache;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('InstitutionBundle:Institution');
        $this->discriminatorMapping = InstitutionTypes::getDiscriminatorMapping();
    }
    
    public function setMemcacheNamespacePrefixStorage(NamespacePrefixStorage $storage)
    {
        $this->memcacheNamespacePrefixStorage = $storage;
        $this->memcache = $this->memcacheNamespacePrefixStorage->getMemcacheService();
    }
    
    /**
     * Create an instance of Institution by discriminator type
     * 
     * @param int $type
     * @return Institution
     */
    public function createByType($type)
    {
        if (!\array_key_exists($type, $this->discriminatorMapping)) {
            throw InstitutionFactoryException::invalidDiscriminator($type);
        }
        $cls = $this->discriminatorMapping[$type];
        
        return new $cls;
    }
    
    /**
     * Layer for Doctrine findOneBy slug
     * 
     * @param string $slug
     * @return Institution
     */
    public function findBySlug($slug)
    {
        return $this->repository->findOneBy(array('slug' => $slug));
    }
    
    /**
     * Layer for Doctrine find by id
     * 
     * @param int $id
     * @return Institution
     */
    public function findById($id)
    {
        // get namespace key for institution with id $id
        $institutionNamespace = $this->memcacheNamespacePrefixStorage->getNamespaceByConfigKey('institution.base', $id);
        $memcacheKey = $institutionNamespace.'_entity';
        $result = $this->memcache->get($memcacheKey);
        if (!$result){
            $result = $this->repository->find($id);
            
            // add to memcache
            $this->memcache->set($memcacheKey, $result);
        }
        
        return $result;
    }
    
    /**
     * Save institution
     * 
     * @param Institution $institution
     */
    public function save(Institution $institution)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($institution);
        $em->flush();
    }
}