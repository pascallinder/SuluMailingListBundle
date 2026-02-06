<?php

namespace Linderp\SuluMailingListBundle\Mail\Wrapper;

use Linderp\SuluMailingListBundle\Mail\MailMetadataXmlConfigurationInterface;

class MailWrapperTypeConfiguration implements MailMetadataXmlConfigurationInterface
{
    private int $priority = 0;
    /**
     * @var array<string,string>
     */
    private array $contentKeys = ['mailingListMail.props.content.components'=>'components'];
    public function __construct(private readonly string $title, private readonly string $xmlPath,
                                private readonly string $key){

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
    /**
     * @return array<string,string>
     */
    public function getContentKeys(): array{
        return $this->contentKeys;
    }

    /**
     * @param array<string, string> $contentKeys
     */
    public function setContentKeys(array $contentKeys): static
    {
        $this->contentKeys = $contentKeys;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

}