<?php
namespace Linderp\SuluMailingListBundle\Mail\Wrapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('mailing.wrapper-type')]
interface MailWrapperTypeInterface
{
    public function getConfiguration(): MailWrapperTypeConfiguration;

    public function build(array $wrapper, string $locale):array;
}