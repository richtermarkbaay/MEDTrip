<?php
/**
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
namespace HealthCareAbroad\MailerBundle\Services;

interface MailerInterface
{
    function sendMessage($data);
}