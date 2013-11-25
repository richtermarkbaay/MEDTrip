<?php

namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;

use HealthCareAbroad\StatisticsBundle\Entity\InstitutionStatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

class InstitutionTracker extends Tracker
{
    static $instance;
    
    static public function createInstance()
    {
        if(!static::$instance)
            static::$instance = new self;

        return static::$instance;
    }
    
    public function createDataFromParameters(StatisticsParameterBag $parameters)
    {
        var_dump($parameters->get('institutionSlug'));
        exit;
    }

    public function createFullPageViewDataFromSlug(array $data)
    {
        $data['categoryId'] = StatisticCategories::HOSPITAL_FULL_PAGE_VIEW;
        $data = $this->createData($data);

        return $data;
    }
    
    private function createData(array $data)
    {
        $data = new InstitutionStatisticsDaily();
        $data->setInstitutionId($data['institutionId']);
        $data->setCategoryId($data['categoryId']);
        $data->setIpAddress($data['ipAddress']);
        $data->setDate(new \DateTime());

        return $data;
    }
    
    public function add(StatisticsDaily $data)
    {
        $em = $this->doctrine->getEntityManagerForClass('StatisticsBundle:InstitutionStatiticsDaily');

        $em->persist($data);
        $em->flush();
    }
}