<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use Doctrine\ORM\EntityRepository;

class InstitutionPropertyTypeRepository extends EntityRepository
{
    const ANCILLIARY_SERVICE = 1;
    const GLOBAL_AWARD = 3;
}