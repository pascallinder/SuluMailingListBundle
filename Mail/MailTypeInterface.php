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

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function build(array $item, string $locale): array;
}
