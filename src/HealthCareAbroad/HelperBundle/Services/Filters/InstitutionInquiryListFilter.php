<?php 

/**
 * @author Adelbert Silla
 */
namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

class InstitutionInquiryListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->addValidCriteria('country');
        $this->addValidCriteria('institution');
        $this->addValidCriteria('institutionCountry');
        $this->addValidCriteria('institutionsOrClinics');

        $this->sortOrder = 'desc';
        $this->serviceDependencies = array('services.location');
    }

    function setFilterOptions()
    {
        $this->setInstitutionsOrClinicsFilterOption();
        $this->setInstitutionCountryFilterOption();
        $this->setCountryFilterOption();
        $this->setInstitutionFilterOption();
    }

    function setInstitutionCountryFilterOption()
    {
        $countries = $this->getInjectedDependcy('services.location')->getGlobalCountries();
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);

        foreach($countries['data'] as $each) {
            $options[$each['id']] = $each['name'];
        }

        $this->filterOptions['institutionCountry'] = array(
            'label' => 'Institution Country',
            'selected' => $this->queryParams['institutionCountry'],
            'options' => $options
        );
    }

    function setCountryFilterOption()
    {
        $countries = $this->getInjectedDependcy('services.location')->getGlobalCountries();
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
    
        foreach($countries['data'] as $each) {
            $options[$each['id']] = $each['name'];
        }
    
        $this->filterOptions['country'] = array(
            'label' => 'Country',
            'selected' => $this->queryParams['country'],
            'options' => $options
        );
    }

    function setInstitutionFilterOption()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('a.id, a.name')
           ->from('InstitutionBundle:Institution', 'a')
           ->where('a.status = :status')
           ->setParameter('status', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
           ->orderBy('a.name', 'ASC');

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

    function setInstitutionsOrClinicsFilterOption()
    {
        $options = array(
            ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL,
            1 => 'Iquiries for Institutions Only',
            2 => 'Iquiries for Clinics Only'
        );

        $this->filterOptions['institutionsOrClinics'] = array(
            'label' => 'Inquiries For Institutions or Clinics',
            'selected' => $this->queryParams['institutionsOrClinics'],
            'options' => $options
        );
    }

    function setFilteredResults()
    {
        $this->queryBuilder = $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a, institution, institutionMedicalCenter, institutionCountry, country')->from('InstitutionBundle:InstitutionInquiry', 'a');
        $this->queryBuilder->leftJoin('a.institution', 'institution');
        $this->queryBuilder->leftJoin('institution.country', 'institutionCountry');
        $this->queryBuilder->leftJoin('a.institutionMedicalCenter', 'institutionMedicalCenter');
        $this->queryBuilder->leftJoin('a.country', 'country');

        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }

        if ($this->queryParams['institutionCountry'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('institution.country = :institutionCountry');
            $this->queryBuilder->setParameter('institutionCountry', $this->queryParams['institutionCountry']);
        }

        if ($this->queryParams['institution'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.institution = :institution');
            $this->queryBuilder->setParameter('institution', $this->queryParams['institution']);
        }
        
        if ($this->queryParams['institutionsOrClinics'] != ListFilter::FILTER_KEY_ALL) {
            if((int)$this->queryParams['institutionsOrClinics'] === 1) {
                $this->queryBuilder->andWhere('a.institutionMedicalCenter IS NULL');
            } else if((int)$this->queryParams['institutionsOrClinics'] === 2) {
                $this->queryBuilder->andWhere('a.institutionMedicalCenter IS NOT NULL');                
            }
        }

        $sortBy = $this->sortBy ? $this->sortBy : 'dateCreated';
    	$sort = (strrpos($sortBy, '.') !== false ? "$sortBy " : "a.$sortBy ") . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}