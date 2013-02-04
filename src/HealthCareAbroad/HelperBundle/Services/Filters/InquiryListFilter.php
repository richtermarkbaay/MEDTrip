<?php 
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Symfony\Component\Validator\Constraints\Date;

use HealthCareAbroad\AdminBundle\Entity\InquirySubject;

class InquiryListFilter extends ListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    $this->addValidCriteria('subject');
    $this->addValidCriteria('dateCreated');
    
    }
    function setFilterOptions()
    {
        $this->setInquiryFilterOption();
        $this->setDateCreatedFilterOption();
    }
    
    function setInquiryFilterOption()
    {
        $subjects = $this->doctrine->getEntityManager()->getRepository('AdminBundle:InquirySubject')->findBy(array('status' => InquirySubject::STATUS_ACTIVE),array('name' => 'ASC'));
    
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($subjects as $each) {
            $options[$each->getId()] = $each->getName();
        }
    
        $this->filterOptions['subject'] = array(
                        'label' => 'Subject',
                        'selected' => $this->queryParams['subject'],
                        'options' => $options
        );
    }
    function setDateCreatedFilterOption()
    {
        $dateOptions = date("m/d/y");
    
        $this->filterOptions['dateCreated'] = array(
                        'label' => 'Date Created',
                        'value' => $dateOptions
        );
    }
    
    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('AdminBundle:Inquiry', 'a');
    
        if ($this->queryParams['dateCreated'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.dateCreated >= :dateCreated');
            $this->queryBuilder->setParameter('dateCreated', date("Y-m-d", strtotime($this->queryParams['dateCreated'])) );
        }
        if ($this->queryParams['subject'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.inquirySubject = :subject');
            $this->queryBuilder->setParameter('subject', $this->queryParams['subject']);
        }

        if($this->sortBy == 'firstName') {
        		$sortBy = $this->sortBy ? $this->sortBy : 'firstName';
        	$sort = "a.$sortBy " . $this->sortOrder;
        } else {
        	$sortBy = $this->sortBy ? $this->sortBy : 'dateCreated';
        	$sort = "a.$sortBy " . $this->sortOrder;
        }            

        $this->queryBuilder->add('orderBy', $sort);
    }
}