<?php
namespace Linderp\SuluMailingListBundle\Mail\Resource;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('mailing.resource')]
interface MailResourceInterface
{
    public function getConfiguration(): MailResourceConfiguration;
}