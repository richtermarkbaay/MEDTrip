<?php 
/**
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

class InstitutionStatisticsListFilter extends NativeQueryListFilter
{    
    function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

        parent::__construct($doctrine->getConnection('statistics'));

        // Add country in validCriteria
        $this->addValidCriteria('category');
        $this->addValidCriteria('name');
        $this->addValidCriteria('date');
        $this->addValidCriteria('dateYear');
        $this->addValidCriteria('reportType');
        $this->addValidCriteria('limit');

        $this->defaultParams['name'] = '';
        $this->defaultParams['category'] = StatisticCategories::HOSPITAL_FULL_PAGE_VIEW;
        $this->defaultParams['date'] = date('Y/m/d');
        $this->defaultParams['dateYear'] = '';
        $this->defaultParams['reportType'] = 1;
        $this->defaultParams['limit'] = $this->pagerDefaultOptions['limit'];

        $this->sortBy = 'name';
    }
    function setFilterOptions()
    {
        $this->setCategoryFilterOption();
        $this->setReportTypeOption();
        $this->setDateFilterOption();
        $this->setDateYearFilterOption();
        $this->setNameFilterOption();
        $this->setLimitFilterOption();
    }
    
    function setNameFilterOption()
    {
        $this->filterOptions['name'] = array('label' => 'Institution Name', 'value' => $this->queryParams['name']);
    }

    function setCategoryFilterOption()
    {
        $categories = $this->doctrine->getEntityManager('statistics')->getRepository('StatisticsBundle:Category')->findByType(StatisticTypes::INSTITUTION);

        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($categories as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['category'] = array(
            'label' => 'Category',
            'selected' => $this->queryParams['category'],
            'options' => $options
        );
    }

    function setDateFilterOption()
    {
        $this->filterOptions['date'] = array(
            'label' => 'Date',
            'value' => $this->queryParams['date'],
            'placeholder' => 'yyyy/mm/dd'
        );
    }
    
    function setDateYearFilterOption()
    {
        $this->filterOptions['dateYear'] = array(
            'label' => 'Year',
            'value' => $this->queryParams['dateYear'],
            'placeholder' => 'yyyy'
        );
    }

    function setReportTypeOption()
    {
        $this->filterOptions['reportType'] = array(
            'label' => 'Report Type',
            'selected' => $this->queryParams['reportType'],
            'options' => array(1 => 'Daily', 2 => 'Annual')
        );
    }

    function setLimitFilterOption()
    {
        $this->filterOptions['limit'] = array(
            'label' => 'Limit',
            'selected' => $this->queryParams['limit'],
            'options' => array(10 => 10, 20 => 20, 50 => 50, 100 => 100, 200 => 200, 300 => 300, 500 => 500, '' => 'All')
        );
    }

    function setFilteredResults()
    {
        $query = 'SELECT a.*, b.name, b.slug, b.institution_type, c.name as category FROM institution_statistics_annual a LEFT JOIN healthcareabroad.institutions b on a.institution_id = b.id LEFT JOIN categories c ON a.category_id = c.id';
        $countQuery = 'select count(*) as count from institution_statistics_annual a';

        $params = array();

        if($this->queryParams['date']) {
            $params['date'] = $this->queryParams['date'];
        }
        
        if($this->queryParams['dateYear']) {
            $prevYear = $this->queryParams['dateYear'] - 1;
            $nextYear = $this->queryParams['dateYear'] + 1;
            $params['date'] = array('BETWEEN' => array($prevYear ."/12/31", $nextYear . "/01/01")) ;
        }

        if($this->queryParams['category'] != 'all') {
            $params['category_id'] = $this->queryParams['category'];
            $countQuery .= ' LEFT JOIN categories c ON a.category_id = c.id';
        }

        if($this->queryParams['name']) {
            $params['b.name'] = array('LIKE' => '%' . $this->queryParams['name'] . '%');
            $countQuery .= ' LEFT JOIN healthcareabroad.institutions b ON a.institution_id = b.id';
        }
        
        if($this->queryParams['date'] && !$this->queryParams['dateYear']) {
            $params['date'] = $this->queryParams['date'];
        }
 
        $sortBy = $this->sortBy ? $this->sortBy : 'date';
        $sort = "$sortBy $this->sortOrder";

        if((int)$this->queryParams['reportType'] === 2) {
            $query = str_replace(' FROM', ', SUM(total) as total_sum FROM', $query);
            $groupBy = (int)$this->queryParams['reportType'] === 1 ? null : 'institution_id';
        } else {
            $groupBy = '';
        }

        $this->pagerAdapter->setQueriesAndQueryParams($query, $countQuery, $params, $sort, $groupBy);

        $this->filteredResult = $this->pager->getResults();
    }
}