<?php

namespace Linderp\SuluMailingListBundle\Mail\Resource\Types;

use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceConfiguration;
use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceInterface;

class MailTemplateFooterResource implements MailResourceInterface
{
    public function getConfiguration(): MailResourceConfiguration
    {
        return new MailResourceConfiguration(
            __DIR__ . '/../../../Resources/config/forms/mail_template_footer_details.xml',
            false,
        );
    }
}
