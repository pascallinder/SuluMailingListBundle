<?php

namespace Linderp\SuluMailingListBundle\Mail\Resource\Types;

use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceConfiguration;
use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceInterface;

class DoubleOptMailResource implements MailResourceInterface
{
    public function getConfiguration(): MailResourceConfiguration
    {
        return new MailResourceConfiguration(
            __DIR__ . '/../../../Resources/config/forms/newsletter_double_opt_details.xml',
            true
        );
    }
}