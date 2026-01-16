<?php

namespace Linderp\SuluMailingListBundle\Entity;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluBaseBundle\Entity\IdTrait;
use Linderp\SuluBaseBundle\Entity\LocaleTrait;
use Sulu\Bundle\MediaBundle\Entity\Media;

abstract class MailTranslatable
{
    use IdTrait;
    use LocaleTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ["defaults" => 'no-reply@refashion.ch'])]
    protected string $senderMail;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $mailTemplate;
    
    public abstract function getTranslations():Collection;
    public function hasTranslation(string $locale):bool{
        return $this->getTranslations()->containsKey($locale);
    }
    protected function getTranslation(string $locale): ?MailTranslation
    {
        if (!$this->getTranslations()->containsKey($locale)) {
            return null;
        }
        return $this->getTranslations()->get($locale);
    }

    protected abstract function createTranslation(string $locale);

    public function getTitle(?string $locale = null): ?string
    {
        $translation = $this->getTranslation($locale ?? $this->locale);
        if (!$translation instanceof MailTranslation) {
            return null;
        }
        return $translation->getTitle();
    }

    public function setTitle(?string $title): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof MailTranslation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setTitle($title);
        return $this;
    }

    public function getSubject(?string $locale = null): ?string
    {
        $translation = $this->getTranslation($locale ?? $this->locale);
        if (!$translation instanceof MailTranslation) {
            return null;
        }
        return $translation->getSubject();
    }

    public function setSubject(string $title): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof MailTranslation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setSubject($title);
        return $this;
    }

    public function getBody(?string $locale = null): ?string
    {
        $translation = $this->getTranslation($locale ?? $this->locale);
        if (!$translation instanceof MailTranslation) {
            return null;
        }
        return $translation->getBody();
    }

    public function setBody(string $body): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof MailTranslation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setBody($body);
        return $this;
    }
    public function getHeaderImage(?string $locale = null): ?Media
    {
        $translation = $this->getTranslation($locale ?? $this->locale);
        if (!$translation instanceof MailTranslation) {
            return null;
        }
        return $translation->getHeaderImage();
    }

    public function setHeaderImage(?Media $headerImage): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof MailTranslation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setHeaderImage($headerImage);
        return $this;
    }
    /**
     * @param Collection $translations
     */
    public abstract function setTranslations(Collection $translations): void;

    /**
     * @return string|null
     */
    public function getMailTemplate(): ?string
    {
        return $this->mailTemplate;
    }

    /**
     * @param string|null $mailTemplate
     */
    public function setMailTemplate(?string $mailTemplate): void
    {
        $this->mailTemplate = $mailTemplate;
    }

    /**
     * @return string
     */
    public function getSenderMail(): string
    {
        return $this->senderMail;
    }

    /**
     * @param ?string $senderMail
     */
    public function setSenderMail(?string $senderMail): void
    {
        $this->senderMail = $senderMail ?? 'no-reply@refashion.ch';
    }
}