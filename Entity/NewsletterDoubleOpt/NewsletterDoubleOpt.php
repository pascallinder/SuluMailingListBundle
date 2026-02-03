<?php

namespace Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;

#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
class NewsletterDoubleOpt extends MailTranslatable
{
    #[ORM\OneToMany(mappedBy: 'newsletterDoubleOpt', targetEntity: NewsletterDoubleOptTranslation::class, cascade: ['persist'], fetch: 'EAGER', indexBy: 'locale')]
    protected Collection $translations;

    #[ORM\OneToOne(mappedBy: 'newsletterDoubleOpt', targetEntity: Newsletter::class, cascade: ['persist','remove'])]
    private ?Newsletter $newsletter = null;

    public function __construct(){
        $this->translations = new ArrayCollection();
    }
    /**
     * @return string|null
     */
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

    protected function createTranslation(string $locale): NewsletterDoubleOptTranslation
    {
        $translation = new NewsletterDoubleOptTranslation($this, $locale);
        $this->translations->set($locale, $translation);
        return $translation;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function setTranslations(Collection $translations): void
    {
       $this->translations = $translations;
    }

    public function copy(): static
    {
        $dest = new self();
        $dest->applyFrom($this);
        return $dest;
    }

    protected function getTranslationClass(): string
    {
        return NewsletterDoubleOptTranslation::class;
    }
}