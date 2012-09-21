<?php
/**
 * Unit test for Advertisement factory
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Tests\Services;

use HealthCareAbroad\AdvertisementBundle\Entity\FeaturedListingAdvertisement;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementFactory;

use HealthCareAbroad\AdvertisementBundle\Entity\FeaturedInstitutionAdvertisement;

use HealthCareAbroad\AdvertisementBundle\Tests\AdvertisementBundleUnitTestCase;

class AdvertisementFactoryTest extends AdvertisementBundleUnitTestCase
{
    /**
     * @var AdvertisementFactory
     */
    private $factory;
    
    public function setUp()
    {
        $this->factory = new AdvertisementFactory($this->getServiceContainer());
    }
    
    public function testCreateInstanceByType()
    {
        $this->markTestIncomplete();
    }
    
    public function testCreateAdvertisementTypeSpecificForm()
    {
        // test featured institution advertisement
        $advertisement = new FeaturedInstitutionAdvertisement();
        $this->assertInstanceOf('HealthCareAbroad\AdvertisementBundle\Form\FeaturedInstitutionAdvertisementFormType', $this->factory->createAdvertisementTypeSpecificForm($advertisement));
        
        // test featured listing advertisement
        $advertisement = new FeaturedListingAdvertisement();
        $this->assertInstanceOf('HealthCareAbroad\AdvertisementBundle\Form\FeaturedListingAdvertisementFormType', $this->factory->createAdvertisementTypeSpecificForm($advertisement));
    }
}