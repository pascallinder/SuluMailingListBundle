<?php

namespace Linderp\SuluMailingListBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
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

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    protected string $senderMail;

    /**
     * @return Collection<MailTranslation>
     */

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

    public function getContent(?string $locale = null): ?array
    {
        $translation = $this->getTranslation($locale ?? $this->locale);
        if (!$translation instanceof MailTranslation) {
            return null;
        }
        return $translation->getContent();
    }

    public function setContent(?array $content): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof MailTranslation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setContent($content);
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
    /**
     * @param Collection<MailTranslation> $translations
     */
    public abstract function setTranslations(Collection $translations): void;

    /**
     * @return Collection<MailTranslation>
     */
    public abstract function getTranslations():Collection;


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
        $this->senderMail = $senderMail;
    }
    public function applyFrom(self $source): void
    {
        $this->setSenderMail($source->getSenderMail());
        foreach ($source->getTranslations() as $translation) {
            $copy= $this->createTranslation($translation->getLocale());
            $copy->applyFrom($translation);
        }
    }
    protected abstract function getTranslationClass(): string;
    public abstract function copy(): static;
}