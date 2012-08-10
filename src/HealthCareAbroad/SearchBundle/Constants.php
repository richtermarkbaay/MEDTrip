<?php
namespace HealthCareAbroad\SearchBundle;

interface Constants
{
	const SEARCH_CATEGORY_INSTITUTION =				1;
	const SEARCH_CATEGORY_CENTER =					2;
	const SEARCH_CATEGORY_PROCEDURE_TYPE =			3;
	const SEARCH_CATEGORY_PROCEDURE =				4;
	
	//TODO: use localization facilities of symfony instead
	const SEARCH_CATEGORY_LABEL_INSTITUTION =		'Institution';
	const SEARCH_CATEGORY_LABEL_CENTER =			'Medical Center';
	const SEARCH_CATEGORY_LABEL_PROCEDURE_TYPE =	'Procedure Type';
	const SEARCH_CATEGORY_LABEL_PROCEDURE =			'Procedure';
}