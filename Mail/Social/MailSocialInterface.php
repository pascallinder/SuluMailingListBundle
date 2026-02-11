<?php

namespace Linderp\SuluMailingListBundle\Mail\Social;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('mailing.social')]
interface MailSocialInterface
{
    public function getConfiguration(): MailSocialConfiguration;
}