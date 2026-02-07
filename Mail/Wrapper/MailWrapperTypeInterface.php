<?php
namespace Linderp\SuluMailingListBundle\Mail\Wrapper;
use Linderp\SuluMailingListBundle\Mail\MailTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @extends MailTypeInterface<MailWrapperTypeConfiguration>
 */
#[AutoconfigureTag('mailing.wrapper-type')]
interface MailWrapperTypeInterface extends MailTypeInterface
{}