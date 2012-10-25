<?php 
namespace HealthCareAbroad\TreatmentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class SpecializationStatusToBooleanTransformer implements DataTransformerInterface
{
    /**
     * Transforms the Specialization status property (int) to a boolean field type.
     *
     * @param  int $status
     * @return boolean|null
     */
    public function transform($status)
    {
    	if (!is_numeric($status) || ($status != 1 && $status != 0)) {
    		return null;
    	}
    	
		return $status ? true : false;
    }

    /**
     * Transforms a boolean $isActivated to an integer.
     *
     * @param  boolean $isActivated
     * @return integer
     */
    public function reverseTransform($isActivated)
    {
		return $isActivated ? 1 : 0;
    }
}