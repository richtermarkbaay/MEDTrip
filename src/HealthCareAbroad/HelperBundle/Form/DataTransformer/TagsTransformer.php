<?php 

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\HelperBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;

class TagsTransformer implements DataTransformerInterface
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
     * Transforms tags object to string tags separated by comma.
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
     * Transforms a string (tags) to an array object (tag).
     *
     * @param  string $stringTags
     * @return ArrayCollection|null
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