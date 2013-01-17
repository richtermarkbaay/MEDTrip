<?php
namespace HealthCareAbroad\HelperBundle\Twig;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Extension for filters or functions that needs a url generator
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
class UrlGeneratorTwigExtension extends \Twig_Extension
{
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function getFunctions()
    {
        return array(
            'get_treatment_url' => new \Twig_Function_Method($this, 'get_treatment_url'),
        );
    }

    public function get_treatment_url(Treatment $treatment)
    {
        return $this->generator->generate('search_frontend_results_treatments', array(
            'specialization' => $treatment->getSpecialization()->getSlug(),
            'treatment' => $treatment->getSlug()
        ), true);
    }

    public function getName()
    {
        return 'url_generator';
    }
}