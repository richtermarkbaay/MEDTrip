<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\QueryBuilder;

class CityListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityRepository = $doctrine->getEntityManager()->getRepository('HelperBundle:City');

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
        $options = array('all' => 'All');
        foreach($countries as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['country'] = array(
            'label' => 'Country',
            'selected' => $this->queryParams['country'],
            'options' => $options
        );
    }

    function setQueryBuilder()
    {
        $this->queryBuilder = new QueryBuilder($this->doctrine->getEntityManager());
        $this->queryBuilder->select('c')->from('HelperBundle:City', 'c');

        if ($this->queryParams['country'] != 'all') {
            $this->queryBuilder->where('c.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }

        if ($this->queryParams['status'] != 'all') {
            $this->queryBuilder->andWhere('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
    }
}