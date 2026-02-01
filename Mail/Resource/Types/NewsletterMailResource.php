<?php

namespace Linderp\SuluMailingListBundle\Mail\Resource\Types;

use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceConfiguration;
use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceInterface;

class NewsletterMailResource implements MailResourceInterface
{
    public function getConfiguration(): MailResourceConfiguration
    {
        return new MailResourceConfiguration(
            __DIR__ . '/../../../Resources/config/forms/newsletter_mail_details.xml',
            false
        );
    }
}