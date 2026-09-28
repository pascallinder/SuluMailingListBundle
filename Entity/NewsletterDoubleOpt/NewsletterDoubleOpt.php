<?php

namespace Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTemplateAwareInterface;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt\NewsletterDoubleOptRepository;

#[ORM\Entity(repositoryClass: NewsletterDoubleOptRepository::class)]
class NewsletterDoubleOpt extends MailTranslatable implements MailTemplateAwareInterface
{
    /**
     * @var Collection<string, MailTranslation>
     */
    #[ORM\OneToMany(mappedBy: 'newsletterDoubleOpt', targetEntity: NewsletterDoubleOptTranslation::class, cascade: ['persist'], fetch: 'EAGER', indexBy: 'locale')]
    protected Collection $translations;

    #[ORM\OneToOne(mappedBy: 'newsletterDoubleOpt', targetEntity: Newsletter::class, cascade: ['persist','remove'])]
    private ?Newsletter $newsletter = null;

    #[ORM\ManyToOne(targetEntity: MailTemplateHeader::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?MailTemplateHeader $header = null;

    #[ORM\ManyToOne(targetEntity: MailTemplateFooter::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private MailTemplateFooter $footer;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }
    /**
     * @return Newsletter|null
     */
    public function getNewsletter(): ?Newsletter
    {
        return $this->newsletter;
    }

    /**
     * @param Newsletter|null $newsletter
     */
    public function setNewsletter(?Newsletter $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    public function getHeader(): ?MailTemplateHeader
    {
        return $this->header;
    }

    public function setHeader(?MailTemplateHeader $header): void
    {
        $this->header = $header;
    }

    public function getFooter(): ?MailTemplateFooter
    {
        return $this->footer ?? null;
    }

    public function setFooter(?MailTemplateFooter $footer): void
    {
        if (!$footer instanceof MailTemplateFooter) {
            throw new \InvalidArgumentException('A mail footer is required.');
        }
        $this->footer = $footer;
    }

    protected function createTranslation(string $locale): NewsletterDoubleOptTranslation
    {
        $translation = new NewsletterDoubleOptTranslation($this, $locale);
        $this->translations->set($locale, $translation);
        return $translation;
    }

    /**
     * @return Collection<string, MailTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * @param Collection<string, MailTranslation> $translations
     */
    public function setTranslations(Collection $translations): void
    {
        $this->translations = $translations;
    }

    public function copy(): self
    {
        $dest = new self();
        $dest->applyFrom($this);
        $dest->setHeader($this->getHeader());
        $dest->setFooter($this->getFooter());
        return $dest;
    }

    protected function getTranslationClass(): string
    {
        return NewsletterDoubleOptTranslation::class;
    }
}
