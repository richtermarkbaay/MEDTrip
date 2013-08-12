<?php
/**
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\MemcacheBundle\Services\KeyFactory;

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
    
    /**
     * @var KeyFactory
     */
    private $memcacheKeyFactory;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('InstitutionBundle:Institution');
        //$this->discriminatorMapping = InstitutionTypes::getDiscriminatorMapping();
    }
    
    public function setMemcacheKeyFactory(KeyFactory $factory)
    {
        $this->memcacheKeyFactory = $factory;
    }
    
    public function setMemcache(MemcacheService $memcache)
    {
        $this->memcache = $memcache;
    }
    
    public function setMemcacheNamespacePrefixStorage(NamespacePrefixStorage $storage)
    {
        $this->memcacheNamespacePrefixStorage = $storage;
        $this->memcache = $this->memcacheNamespacePrefixStorage->getMemcacheService();
    }
    
    /**
     * Get all approved institutions
     * 
     * @return array Institution
     */
    public function findAllApproved()
    {
        $query = $this->repository->getQueryBuilderForApprovedInstitutions()
            ->getQuery();

        return $query->getResult();
    }
    
    /**
     * Create new instance of Institution
     * 
     * @return \HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    public function createInstance()
    {
        return new Institution();    
    }
    
    /**
     * Create an instance of Institution by discriminator type
     * 
     * @param int $type
     * @return Institution
     */
//     public function createByType($type)
//     {
//         if (!\array_key_exists($type, $this->discriminatorMapping)) {
//             throw InstitutionFactoryException::invalidDiscriminator($type);
//         }
//         $cls = $this->discriminatorMapping[$type];
        
//         return new $cls;
//     }
    
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
//         $memcacheKey = $this->memcacheKeyFactory->generate('institution_entity', array('id' => $id), array('institutionId' => $id));
//         //$result = $this->memcache->get($memcacheKey);
//         $result = false;
//         if (!$result) {
//             $result = $this->repository->find($id);
            
//             // save this to memcache
//             $this->memcache->set($memcacheKey, $result);
//         }

        // no caching here
        $result = $this->repository->find($id);
        
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
        
        // get the memcache key for this institution
//         $memcacheKey = $this->memcacheKeyFactory->generate('institution_entity', array('id' => $institution->getId()), array('institutionId' => $institution->getId()));
        
//         // delete this from memcache
//         $this->memcache->delete($memcacheKey);
    }
}