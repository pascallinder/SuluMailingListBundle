<?php
namespace Linderp\SuluMailingListBundle\Mail\Field;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('mailing.field-type')]
interface MailFieldTypeInterface
{
    public function getConfiguration(): MailFieldTypeConfiguration;

    public function build(array $item, string $locale):array;
}