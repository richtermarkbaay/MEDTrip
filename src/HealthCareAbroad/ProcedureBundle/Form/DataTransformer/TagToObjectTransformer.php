<?php 

namespace HealthCareAbroad\ProcedureBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use HealthCareAbroad\HelperBundle\Entity\Tag;

class TagToObjectTransformer implements DataTransformerInterface
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
     * Transforms string names to object (tag).
     *
     * @param ArrayCollection $tags
     * @return string
     */
    public function transform($tags)
    {

    	
    	if(!count($tags)) {
    		return null;
    	}
    	
    	$tagsName = array();
		foreach($tags as $tag) {
			$tagsName[] = $tag->getName();
		}
		
		return implode(', ', $tagsName);
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $number
     * @return Issue|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($stringTags)
    {
    	$tagObjects = new ArrayCollection();

    	if($stringTags == '')
    		return $tagObjects;

    	$tags = explode(',', $stringTags);

    	foreach($tags as $tagName) {
    		$tag = $this->em->getRepository('HelperBundle:Tag')->findOneBy(array('name'=>trim($tagName)));
    		if($tag) $tagObjects->add($tag);
    	}

//         if (null === $issue) {
//             throw new TransformationFailedException(sprintf(
//                 'An issue with number "%s" does not exist!',
//                 $number
//             ));
//         }

		return $tagObjects;
    }
}