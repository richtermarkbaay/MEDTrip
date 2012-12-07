<?php
namespace HealthCareAbroad\SearchBundle;

interface Constants
{
	const SEARCH_CATEGORY_INSTITUTION =				1;
	const SEARCH_CATEGORY_CENTER =					2;
	const SEARCH_CATEGORY_PROCEDURE_TYPE =			3;//Treatments
	const SEARCH_CATEGORY_PROCEDURE =				4;//Treatment Procedure
	const SEARCH_CATEGORY_DOCTOR =				    5;
	const SEARCH_CATEGORY_SPECIALIZATION =			6;
	const SEARCH_CATEGORY_SUB_SPECIALIZATION =	    7;
	
	
	//TODO: use localization facilities of symfony instead
	const SEARCH_CATEGORY_LABEL_INSTITUTION =		'Institution';
	const SEARCH_CATEGORY_LABEL_CENTER =			'Medical Center';
	const SEARCH_CATEGORY_LABEL_PROCEDURE_TYPE =	'Procedure Type';
	const SEARCH_CATEGORY_LABEL_PROCEDURE =			'Procedure';
	const SEARCH_CATEGORY_LABEL_DOCTOR =            'Doctor';
	const SEARCH_CATEGORY_LABEL_SPECIALIZATION =    'Specialization';
	const SEARCH_CATEGORY_LABEL_SUB_SPECIALIZATION ='Sub-Specialization';
}