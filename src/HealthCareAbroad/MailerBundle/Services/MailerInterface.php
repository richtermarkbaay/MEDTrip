<?php
/**
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
namespace HealthCareAbroad\MailerBundle\Services;

interface MailerInterface
{
    /**
     * Sends email.
     *
     * @param array $data
     */
    function sendMessage($data);
}