<?php
namespace HealthCareAbroad\MailerBundle\Services;

class MailChimpService
{
    private $mailchimpClient;

    public function __construct($mailchimpClient)
    {
        $this->mailchimpClient = $mailchimpClient;
    }

    /**
     * Subscribe to a Mailchimp list. By default will use the newsletter list.
     *
     * To test if mailchimp is reachable: $mailChimp->ping();
     *
     * @param string $email
     * @param string $listId
     * @return boolean True on success, false on failure.
     */
    public function listSubscribe($email, $listId = null)
    {
        if (is_null($listId)) {
            //TODO: externalize
            $listId = '6fb06f3765';
        }

        return $this->mailchimpClient->ping();

        //return  $this->mailchimpClient->listSubscribe($listId, $email);
    }
}