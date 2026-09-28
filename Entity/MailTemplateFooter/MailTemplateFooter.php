<?php

namespace Linderp\SuluMailingListBundle\Entity\MailTemplateFooter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Repository\MailTemplateFooter\MailTemplateFooterRepository;

#[ORM\Entity(repositoryClass: MailTemplateFooterRepository::class)]
class MailTemplateFooter extends MailTranslatable
{
    final public const RESOURCE_KEY = 'mail_template_footers';

    /**
     * @var Collection<string, MailTemplateFooterTranslation>
     */
    #[ORM\OneToMany(mappedBy: 'footer', targetEntity: MailTemplateFooterTranslation::class, cascade: ['persist'], indexBy: 'locale')]
    protected Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    protected function createTranslation(string $locale): MailTemplateFooterTranslation
    {
        $translation = new MailTemplateFooterTranslation($this, $locale);
        $this->translations->set($locale, $translation);

        return $translation;
    }

    /**
     * @return Collection<string, MailTranslation>
     */
    public function getTranslations(): Collection
    {
        // @phpstan-ignore-next-line Collection is invariant; every stored value is a MailTranslation subtype.
        return $this->translations;
    }

    /** @param Collection<string, MailTranslation> $translations */
    public function setTranslations(Collection $translations): void
    {
        $this->translations = new ArrayCollection(array_filter(
            $translations->toArray(),
            static fn(MailTranslation $translation): bool => $translation instanceof MailTemplateFooterTranslation,
        ));
    }

    public function copy(): self
    {
        $copy = new self();
        $copy->applyFrom($this);

        return $copy;
    }

    protected function getTranslationClass(): string
    {
        return MailTemplateFooterTranslation::class;
    }
}
