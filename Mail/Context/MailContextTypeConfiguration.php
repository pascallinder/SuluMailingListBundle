<?php

namespace Linderp\SuluMailingListBundle\Mail\Context;

use Linderp\SuluMailingListBundle\Mail\MailMetadataXmlConfiguration;

class MailContextTypeConfiguration extends MailMetadataXmlConfiguration
{
    private array $acceptedResources = [];
    private array $contextVarsKeys = ['backgroundColor'];
    private string $contextVarsDisabledCondition = "__parent.sent == true";

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

    public function getContextVarsDisabledCondition(): string
    {
        return $this->contextVarsDisabledCondition;
    }

    public function setContextVarsDisabledCondition(string $contextVarsDisabledCondition): static
    {
        $this->contextVarsDisabledCondition = $contextVarsDisabledCondition;
        return $this;
    }

}