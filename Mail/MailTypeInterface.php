<?php

namespace Linderp\SuluMailingListBundle\Mail;

/**
 * @template T of MailMetadataXmlConfiguration
 */
interface MailTypeInterface
{
    /**
     * @return T
     */
    public function getConfiguration(): MailMetadataXmlConfiguration;

    public function build(array $item, string $locale):array;
}