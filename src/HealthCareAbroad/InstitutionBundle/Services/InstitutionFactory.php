<?php
/**
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionRepository;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionFactoryException;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Factory class for Institution
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
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('InstitutionBundle:Institution');
        $this->discriminatorMapping = InstitutionTypes::getDiscriminatorMapping();
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
        return $this->repository->find($id);    
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