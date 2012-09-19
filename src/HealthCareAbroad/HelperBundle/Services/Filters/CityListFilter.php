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
        $this->queryBuilder->select('c')->from('HelperBundle:City', 'c');

        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('c.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $this->queryBuilder->add('orderBy', 'c.name ASC');
    }
}