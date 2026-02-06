<?php

namespace Linderp\SuluMailingListBundle\Mail;

interface MailMetadataXmlConfigurationInterface
{
    public function __construct(string $title, string $xmlPath, string $key);
    public function getPriority(): int;
    public function setPriority(int $priority): static;
}