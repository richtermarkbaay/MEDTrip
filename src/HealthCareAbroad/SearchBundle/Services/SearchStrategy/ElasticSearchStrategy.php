<?php
namespace HealthCareAbroad\SearchBundle\Services\SearchStrategy;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use HealthCareAbroad\SearchBundle\Services\SearchStrategy\SearchStrategy;

class ElasticSearchStrategy extends SearchStrategy
{
    //TODO: remove this function
    public function getContainer()
    {
        return $this->container;
    }

    public function search(SearchParameterBag $searchParams)
    {
        $this->isViewReadyResults = false;

        if ($searchParams->has('term') && $searchParams->get('term')) {
            $this->searchAutoComplete($searchParams);
        }

        return $this->results;
    }

    private function searchAutoComplete(SearchParameterBag $searchParams)
    {
        $query = new \Elastica_Query_Text();
        $query->setFieldQuery('name', $searchParams->get('term'));
        $query->setFieldParam('name', 'type', 'phrase_prefix');

        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $this->results = $this->container->get('foq_elastica.finder.destinations')->find($query);
                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $this->results = $this->container->get('foq_elastica.finder.treatments')->find($query);
                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:
                $this->searchAutocompleteCombination($searchParams);
                break;

            default:
                throw new \Exception('Unknown context: '. $searchParams->get('context'));
        }
    }

    private function searchAutoCompleteCombination(SearchParameterBag $searchParams)
    {

    }

    private function searchCombination(SearchParameterBag $searchParams)
    {
        $query = new \Elastica_Query_Text();
        $query->setFieldQuery('name', $searchParams->get('term'));
        $query->setFieldParam('name', 'type', 'phrase_prefix');

        $this->results = $this->container->get('foq_elastica.finder.treatments')->find($query);


        $client = $this->container->get('foq_elastica.client');
        $search = new \Elastica_Search($client);

        $postType = $this->get('foq_elastica.index.acme.post');
        $tagType  = $this->get('foq_elastica.index.acme.tag');

        // add the types to the search
        $search->addType($postType)
        ->addType($tagType);

        $index = $this->get('foq_elastica.index');
        $search->addIndex($index);

        $searchTerm = $request->query->get('terms');

        $postSubjectQuery = new \Elastica_Query_Text();
        $postSubjectQuery->setFieldQuery('subject', $searchTerm);
        $postSubjectQuery->setFieldParam('subject', 'analyzer', 'snowball');

        $tagQuery = new \Elastica_Query_Text();
        $tagQuery->setFieldQuery('tagname', $searchTerm);
        $tagQuery->setFieldParam('tagname', 'analyzer', 'snowball');

        $boolQuery = new \Elastica_Query_Bool();
        $boolQuery->addShould($nameQuery);
        $boolQuery->addShould($keywordsQuery);

        $results = $search->search($boolQuery);

        return array('results' => $results);

    }

    public function getArrayResults()
    {
        $elasticaResults = $this->results->getResults();
        $totalResults = $this->results->getTotalHits();

        $arrayResults = array();
        foreach ($elasticaResults as $elasticaResult) {
            $arrayResults[] = $elasticaResult->getData();
        }

        return $arrayResults;
    }

    public function getObjectResults()
    {
        $elasticaResults = $this->results->getResults();
        $totalResults = $this->results->getTotalHits();

        $objectResults = array();
        foreach ($elasticaResults as $elasticaResult) {
            $objectResult[] = $elasticaResult->getData();
        }

        return $objectResults;
    }

    public function isAvailable()
    {
        try {
            $this->container->get('foq_elastica.client')->getIndex('destinations')->getStatus();
        } catch (\Elastica_Exception_Client $e) {
            return false;
        }

        return true;
    }
}