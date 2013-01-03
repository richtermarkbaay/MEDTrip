<?php
namespace HealthCareAbroad\SearchBundle\ElasticSearch\Repository;

use FOQ\ElasticaBundle\Repository;

class DestinationRepository extends Repository
{
    public function getDestinations($searchTerm)
    {
        $nameQuery = new \Elastica_Query_Text();
        $nameQuery->setFieldQuery('name', $searchTerm);
        $nameQuery->setFieldParam('name', 'type', 'phrase_prefix');

        return $this->find($nameQuery);
    }
}