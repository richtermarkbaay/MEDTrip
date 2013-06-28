<?php

namespace HealthCareAbroad\FrontendBundle;

/**
 * List of event names that will be fired inside the FrontendBundle
 * 
 * @author Allejo Chris G. Velarde
 *
 */
final class FrontendBundleEvents
{
    const ADD_INQUIRY = 'event.frontend.add_inquiry';
    
    /**
     * Mapping for event name and its event class
     * This will be loaded in this bundle's extension configuration
     *
     */
    static public function getClassMap()
    {
        return array(
            self::ADD_INQUIRY => 'HealthCareAbroad\FrontendBundle\Event\InquiryEvent'
        );
    }
}