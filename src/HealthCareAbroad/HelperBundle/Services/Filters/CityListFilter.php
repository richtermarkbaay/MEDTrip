<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class CityListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add country in validCriteria
        $this->addValidCriteria('country');
    }

    function setFilterOptions()
    {
        $this->setCountryFilterOption();

        $this->setStatusFilterOption();
    }

    function setCountryFilterOption()
    {
        // Set The Filter Option
        $countries = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->findByStatus(1);
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($countries as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['country'] = array(
            'label' => 'Country',
            'selected' => $this->queryParams['country'],
            'options' => $options
        );
    }

    function buildQueryBuilder()
    {   
        $this->queryBuilder->select('a')->from('HelperBundle:City', 'a');

        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        if($this->sortBy == 'country') {
            $this->queryBuilder->leftJoin('a.country', 'b');
            $sort = 'b.name ' . $this->sortOrder;
        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;            
        }

        $this->queryBuilder->add('orderBy', $sort);
    }
}