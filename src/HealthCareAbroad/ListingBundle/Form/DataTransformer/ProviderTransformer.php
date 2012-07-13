<?php

namespace HealthCareAbroad\ListingBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use HealthCareAbroad\ProbBundle\Entity\Provider;

class ProviderTransformer implements DataTransformerInterface
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @param ObjectManager $om
	 */
	public function __construct($om)
	{
		$this->om = $om;
	}

	/**
	 * Transforms an object (provider_id) to an integer (provider).
	 *
	 * @param  Provider|null $provider
	 * @return int
	 */
	public function reverseTransform($provider)
	{
		var_dump($provider);
		echo '<hr>'; 
		if (null === $provider) {
			return null;
		}

		return $provider->getId();
	}

	/**
	 * Transforms an integer (provider_id) to an object (provider).
	 *
	 * @param  integer $providerId
	 * @return Provider|null
	 * @throws TransformationFailedException if object (provider) is not found.
	 */
	public function transform($providerId)
	{
		if (!$providerId) {
			return null;
		}

		$provider = $this->om->getRepository('ProviderBundle:Provider')->findOneById($providerId);

		if (null === $provider) {
			throw new TransformationFailedException(sprintf(
					'A provider with id "%s" does not exist!',
					$providerId
			));
		}

		return $provider;
	}
}