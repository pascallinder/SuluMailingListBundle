<?php

namespace Linderp\SuluMailingListBundle\Mail\Font;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('mailing.font')]
interface MailFontInterface
{
    public function getConfiguration(): MailFontConfiguration;
}