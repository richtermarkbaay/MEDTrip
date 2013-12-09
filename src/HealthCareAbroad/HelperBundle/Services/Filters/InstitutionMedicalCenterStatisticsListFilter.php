<?php 
/**
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;

class InstitutionMedicalCenterStatisticsListFilter extends NativeQueryListFilter
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
        $this->addValidCriteria('institution');
        $this->addValidCriteria('limit');

        $this->defaultParams['name'] = '';
        $this->defaultParams['category'] = StatisticCategories::CLINIC_FULL_PAGE_VIEW;
        $this->defaultParams['date'] = date('Y-m-d');
        $this->defaultParams['dateYear'] = '';
        $this->defaultParams['reportType'] = 1;
        $this->defaultParams['institution'] = ListFilter::FILTER_KEY_ALL;
        $this->defaultParams['limit'] = $this->pagerDefaultOptions['limit'];

        $this->sortBy = 'date';
        $this->sortOrder = 'DESC';
    }

    function setFilterOptions()
    {
        $this->setCategoryFilterOption();
        $this->setInstitutionFilterOption();
        $this->setReportTypeOption();
        $this->setDateFilterOption();
        $this->setNameFilterOption();
        $this->setLimitFilterOption();
    }
    
    function setNameFilterOption()
    {
        $this->filterOptions['name'] = array('label' => 'Center Name', 'value' => $this->queryParams['name']);
    }

    function setCategoryFilterOption()
    {
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        $options += StatisticCategories::getInstitutionMedicalCenterCategories();

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
            'placeholder' => 'yyyy-mm-dd'
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
    
    function setInstitutionFilterOption()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('a.id, a.name')
           ->from('InstitutionBundle:Institution', 'a')
           ->where('a.status = :status')
           ->orderBy('a.name', 'ASC')
           ->setParameter('status', InstitutionStatus::getBitValueForActiveAndApprovedStatus());

        $institutions = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($institutions as $each) {
            $options[$each['id']] = $each['name'];
        }
        
        $this->filterOptions['institution'] = array(
            'label' => 'Institution',
            'selected' => $this->queryParams['institution'],
            'options' => $options
        );
    }

    function setLimitFilterOption()
    {
        $this->filterOptions['limit'] = array(
            'label' => 'Limit',
            'selected' => $this->queryParams['limit'],
            'options' => array(10 => 10, 20 => 20, 50 => 50, 100 => 100, 200 => 200, 300 => 300, 500 => 500, 1000 => 1000)
        );
    }

    function setFilteredResults()
    {
        $query = 'SELECT a.*, b.name, b.slug, c.name AS institutionName, c.slug AS institutionSlug, c.institution_type AS institutionType 
                  FROM institution_medical_center_statistics_annual a 
                    LEFT JOIN healthcareabroad.institution_medical_centers b on a.institution_medical_center_id = b.id
                    LEFT JOIN healthcareabroad.institutions c on a.institution_id = c.id';

        $countQuery = 'select count(*) as count from institution_medical_center_statistics_annual a';

        $params = array();

        if($this->queryParams['date']) {
            $params['date'] = $this->queryParams['date'];

            if((int)$this->queryParams['reportType'] === 2) {
                $yearFrom = $params['date'];
                $yearTo = date('Y-m-d', strtotime("$yearFrom + 1 year"));
                $params['date'] = array('BETWEEN' => array($yearFrom, $yearTo));
            }
        }

        if($this->queryParams['category'] != 'all') {
            $params['category_id'] = $this->queryParams['category'];
        }
        
        if($this->queryParams['institution'] != 'all') {
            $params['a.institution_id'] = $this->queryParams['institution'];
            $countQuery .= ' LEFT JOIN healthcareabroad.institutions c on a.institution_id = c.id';
        }

        if($this->queryParams['name']) {
            $params['b.name'] = array('LIKE' => '%' . $this->queryParams['name'] . '%');
            $countQuery .= ' LEFT JOIN healthcareabroad.institution_medical_centers b ON a.institution_medical_center_id = b.id';
        }

        if((int)$this->queryParams['reportType'] === 2) {
            $query = str_replace(' FROM', ', SUM(total) as total_sum FROM', $query);
            $groupBy = (int)$this->queryParams['reportType'] === 1 ? null : 'institution_medical_center_id, YEAR(date)';
            if($this->sortBy == 'total')
                $this->sortBy = 'total_sum';
        } else {
            $groupBy = '';
        }

        $sort = $this->sortBy . ' ' . $this->sortOrder;

        $this->pagerAdapter->setQueriesAndQueryParams($query, $countQuery, $params, $sort, $groupBy);

        $this->filteredResult = $this->pager->getResults();
    }
}