<?php
namespace Linderp\SuluMailingListBundle\Mail\Field;
use Linderp\SuluMailingListBundle\Mail\MailTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @extends MailTypeInterface<MailFieldTypeConfiguration>
 */
#[AutoconfigureTag('mailing.field-type')]
interface MailFieldTypeInterface extends MailTypeInterface
{}