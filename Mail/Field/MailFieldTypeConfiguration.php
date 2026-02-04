<?php

namespace Linderp\SuluMailingListBundle\Mail\Field;

use Linderp\SuluMailingListBundle\Mail\Resource\MailResourceInterface;
use Linderp\SuluMailingListBundle\Mail\Wrapper\MailWrapperTypeInterface;

class MailFieldTypeConfiguration
{
    /** @var string[] $acceptedResources */
    private array $acceptedResources = [];
    /**
     * @param string[] $acceptedWrapper
     */
    private array $acceptedWrapper = [];

    private ?string $visibilityCondition = null;
    public function __construct(private readonly string $title, private readonly string $xmlPath, private readonly string $key)
    {

    }

    public function setAcceptedResources(string ...$acceptedResources): static
    {
        $this->acceptedResources = $acceptedResources;
        return $this;
    }

    public function setAcceptedWrapper(string ...$acceptedWrapper): static
    {
        $this->acceptedWrapper = $acceptedWrapper;
        return $this;
    }
    public function getXmlPath(): string
    {
        return $this->xmlPath;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAcceptedResources(): array
    {
        return $this->acceptedResources;
    }

    /**
     * @return string[]
     */
    public function getAcceptedWrapper(): array
    {
        return $this->acceptedWrapper;
    }

}