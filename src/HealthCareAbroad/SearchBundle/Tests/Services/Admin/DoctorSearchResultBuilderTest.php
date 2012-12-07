<?php
namespace HealthCareAbroad\SearchBundle\Tests\Services\Admin;

use HealthCareAbroad\SearchBundle\Services\Admin\DoctorSearchResultBuilder;

use HealthCareAbroad\SearchBundle\Constants;
use HealthCareAbroad\SearchBundle\Services\Admin\SearchAdminPagerService;
use HealthCareAbroad\SearchBundle\Tests\ContainerAwareUnitTestCase;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\MedicalProcedureBundle\Entity\ProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\Procedure;

class DoctorSearchResultBuilderTest extends ContainerAwareUnitTestCase
{
       public function setUp() {
           
//          $criteria = array(
//                         'term' => $term,
//                         'category' => Constants::SEARCH_CATEGORY_DOCTOR
//                  );
//         $this->extFilterParser = new DoctorSearchResultBuilder($criteria);
        }
    
//     public function testBuildQueryBuilder()
//     {
//         $term = 'Test Doctor';
//         $className = get_class($this->extFilterParser);
//         $this->assertTrue(is_array($actual), 'Method initiate() should return an array');
//         $this->assertNotEmpty($actual, "Searched for \"$term\"");
//         $this->assertInstanceOf(
//                         'HealthCareAbroad\\DoctorBundle\\Entity\\Doctor', $actual['term'],
//                         'Method initiate() should return an array of Doctor objects');
    
//         $term = 'Test Doctor';
//         $actual = array(
//                         'term' => $term,
//                         'category' => Constants::SEARCH_CATEGORY_DOCTOR
//                  );
//         $this->assertNotEmpty($actual, "Searched for \"$term\" (search should be case-insensitive)");
    
//     }
    
}