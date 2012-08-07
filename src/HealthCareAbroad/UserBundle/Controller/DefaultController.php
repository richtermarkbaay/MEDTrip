<?php

namespace HealthCareAbroad\UserBundle\Controller;

use HealthCareAbroad\UserBundle\Event\UserEvents;

use HealthCareAbroad\UserBundle\Event\CreateInstitutionUserEvent;

use HealthCareAbroad\UserBundle\Event\InstitutionUserEvents;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Guzzle\Common\Event;

use Guzzle\Http\Message\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Guzzle\Service\Client;

class DefaultController extends Controller
{
}
