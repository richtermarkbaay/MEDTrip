<?php 


namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;


class InstitutionUniqueNameValidator extends ConstraintValidator
{
	/**
	 * @var Registry
	 */
	private  $doctrine;

    public function __construct(Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }
    
	public function validate($value, Constraint $constraint) {
		
		//$value = $entity->getName();

		$institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->findOneBy(array('name' => $value));
		
		if (!$institution) {
			return;
		}
		
		$this->context->addViolation($constraint->message, array('{{ field }}' => $value));
		
		return;
	}
}