<?php

namespace HealthCareAbroad\HelperBundle\Classes;


class Tokenizer{

	public function generateTokenString()
	{	
		$dateNow = date('Ymdhms');
    	return hash("md5",$dateNow);
    }
    
}