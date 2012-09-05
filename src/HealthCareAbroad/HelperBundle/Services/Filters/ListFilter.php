<?php
/**
 * @author Adelbert D. Silla
 * @desc AbstractClass For ListFilter Classes
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

abstract class ListFilter
{
	protected $doctrine;
	
	protected $entityRepository;

	protected $criteria = array();
	
	protected $queryParams = array();
	
	protected $validCriteria = array('status');
	
	protected $filterOptions = array();
	
	protected $filteredResult = array();

	/**
	 * @desc Default options value for Status Filter Option
	 * @var array
	 */
	protected $statusFilterOptions = array(1 => 'Active', 0 => 'Inactive', 'all' => 'All');


	abstract function setFilterOptions();

	abstract function setFilteredResult();

	/**
	 * @desc Prepare the ListFilter object
	 * @param array $queryParams
	 */
	function prepare($queryParams = array())
	{
		$this->setQueryParamsAndCriteria($queryParams);

		$this->setFilterOptions();
		
		$this->setFilteredResult();
	}

	/**
	 * @desc Sets queryParams and the valid criteria
	 * @param array $queryParams
	 */
	function setQueryParamsAndCriteria($queryParams = array())
	{
		$this->queryParams = $queryParams;

		foreach($this->validCriteria as $key) {

			if(isset($queryParams[$key])) {
				if(!is_null($queryParams[$key]) && $queryParams[$key] != 'all')
					$this->criteria[$key] = $queryParams[$key];
			} 
			
			else $this->queryParams[$key] = 'all';
		}
	}

	/**
	 * @desc Sets Status Filter Option
	 */
	function setStatusFilterOption()
	{
		$this->filterOptions['status'] = array(
			'label' => 'Status', 
			'selected' => $this->queryParams['status'],
			'options' => $this->statusFilterOptions
		);
	}
	
	/**
	 * @desc Add a new valid criteria
	 * @param string $val
	 */
	function addValidCriteria($val)
	{
		array_push($this->validCriteria, $val);
	}

	/**
	 * @return multitype:array
	 */
	function getFilterOptions()
	{
		return $this->filterOptions;
	}

	/**
	 * @return multitype:array object
	 */
	function getFilteredResult()
	{
		return $this->filteredResult;
	}
}