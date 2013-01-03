<?php
namespace HealthCareAbroad\SearchBundle\ElasticSearch\Provider;

use FOQ\ElasticaBundle\Doctrine\ORM\Provider;
use FOQ\ElasticaBundle\Provider\ProviderInterface;

class CountryProvider extends Provider
{
    protected $countryType;

    public function __construct(\Elastica_Type $countryType)
    {
        $this->countryType = $countryType;
    }

    /**
     * Insert the repository objects in the type index
     *
     * @param Closure $loggerClosure
     */
    public function populate(\Closure $loggerClosure = null)
    {
        if ($loggerClosure) {
            $loggerClosure('Indexing countries');
        }

        $document = new \Elastica_Document();
        $document->setData(array('name' => 'Philippines'));
        $this->countryType->addDocument(array($document));
    }
}