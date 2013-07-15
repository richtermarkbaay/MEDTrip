<?php
namespace HealthCareAbroad\MailerBundle\Event;

class MailerBundleEvents
{
    const NOTIFICATIONS_TEST = 'mailer_bundle.notification.test';
    const NOTIFICATIONS_HOSPITAL_CREATED = 'mailer_bundle.notification.hospital_profile_created';
    const NOTIFICATIONS_CLINIC_CREATED = 'mailer_bundle.notification.clinic_created';
    const NOTIFICATIONS_NEW_LISTINGS_APPROVED = 'mailer_bundle.notification.new_listings_approved';
    const NOTIFICATIONS_INQUIRIES = 'mailer_bundle.notification.inquiries';
    const NOTIFICATIONS_PASSWORD_RESET = 'mailer_bundle.notification.password_reset';
    const NOTIFICATIONS_PASSWORD_CONFIRM = 'mailer_bundle.notification.password_confirm';
}