<?php

namespace Linderp\SuluMailingListBundle\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluBaseBundle\Entity\IdTrait;
use Sulu\Bundle\MediaBundle\Entity\Media;

abstract class MailTranslation
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $subject = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    protected ?array $content = null;

    public function __construct(
        #[ORM\Column(type: Types::STRING, length: 5, nullable: false)]
        protected string $locale){

    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getContent():?array
    {
        return $this->content;
    }

    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
    public function applyFrom(self $source): void
    {
        $this->subject = $source->subject;
        $this->content = $source->content;
    }
    public abstract function copyTo(string $destLocale): static;
}