<?php

namespace Linderp\SuluMailingListBundle\Mail\Context;

use Linderp\SuluMailingListBundle\Mail\MailMetadataXmlConfiguration;

class MailContextTypeConfiguration extends MailMetadataXmlConfiguration
{
    private array $acceptedResources = [];
    private array $contextVarsKeys = ['backgroundColor'];

    public function getContextVarsKeys(): array
    {
        return $this->contextVarsKeys;
    }

    public function addContextVarsKeys(string ...$contextVarsKeys): static
    {
        $this->contextVarsKeys = [...$this->contextVarsKeys, ...$contextVarsKeys];
        return $this;
    }

    public function getAcceptedResources(): array
    {
        return $this->acceptedResources;
    }

    public function setAcceptedResources(string ...$acceptedResources): static
    {
        $this->acceptedResources = $acceptedResources;
        return $this;
    }
}