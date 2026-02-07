<?php

namespace Linderp\SuluMailingListBundle\Mail\Context;

use Linderp\SuluMailingListBundle\Mail\MailTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @extends MailTypeInterface<MailContextTypeConfiguration>
 */
#[AutoconfigureTag('mailing.context-type')]
interface MailContextTypeInterface extends MailTypeInterface
{
}