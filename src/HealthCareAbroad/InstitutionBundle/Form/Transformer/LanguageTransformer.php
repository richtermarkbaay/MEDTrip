<?php 

/**
 * Institution Language Transformer
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\AdminBundle\Entity\Langauge;
use Doctrine\ORM\EntityManager;

class LanguageTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms languages object to string tags separated by comma.
     *
     * @param ArrayCollection $tags
     * @return string
     */
    public function transform($languages)
    {
    	if(!count($languages)) {
    		return null;
    	}
    	
    	$languagesName = array();
		foreach($languages as $language) {
			$languagesName[] = $language->getName();
		}
		
		return implode(',', $languagesName);
    }

    /**
     * Transforms a string (language) to an array object (language).
     *
     * @param  string $stringLanguages
     * @return ArrayCollection|null
     */
    public function reverseTransform($stringLanguages)
    {
    	$languageObjects = new ArrayCollection();

    	if($stringLanguages == '')
    		return $languageObjects;

    	$languages = explode(',', $stringLanguages);

    	foreach($languages as $languageName) {
    		$language = $this->em->getRepository('AdminBundle:Language')->findOneBy(array('name'=>trim($languageName)));
    		if($language) $languageObjects->add($language);
    	}

		return $languageObjects;
    }
}