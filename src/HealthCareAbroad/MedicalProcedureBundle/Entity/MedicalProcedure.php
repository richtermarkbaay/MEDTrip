<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure
 */
class MedicalProcedure
{
	
	static $STATUS = array(
		'inactive' => 0,
		'active' => 1
	);

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType
     */
    private $medicalProcedureType;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MedicalProcedure
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return MedicalProcedure
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return MedicalProcedure
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set medicalProcedureType
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureType
     * @return MedicalProcedure
     */
    public function setMedicalProcedureType(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureType = null)
    {
        $this->medicalProcedureType = $medicalProcedureType;
        return $this;
    }

    /**
     * Get medicalProcedureType
     *
     * @return HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType 
     */
    public function getMedicalProcedureType()
    {
        return $this->medicalProcedureType;
    }
    
//     public static function loadValidatorMetadata(ClassMetadata $metadata)
//     {
//     	$metadata->addPropertyConstraint('name', new NotBlank());
    
//     	$metadata->addPropertyConstraint('email', new Email());
    
//     	$metadata->addPropertyConstraint('subject', new NotBlank());
//     	$metadata->addPropertyConstraint('subject', new MaxLength(50));
    
//     	$metadata->addPropertyConstraint('body', new MinLength(50));
//     }
}