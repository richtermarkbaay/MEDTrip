<?php
namespace HealthCareAbroad\LogBundle\Listener\Admin;

use HealthCareAbroad\HelperBundle\Classes\ApplicationContexts;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\LogBundle\Listener\BaseCommonListener;

/**
 * Common Log system listener for events dispatched in the Admin bundle. Logs for this events will be specifically marked as ApplicationContext::INSTITUTION_ADMIN
 *
 * @author Allejo Chris G. Velarde
 */
class AdminBundleCommonListener extends BaseCommonListener
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->applicationContext = ApplicationContexts::HCA_ADMIN;
    }
}