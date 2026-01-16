<?php

namespace Linderp\SuluMailingListBundle\Entity\Newsletter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Linderp\SuluBaseBundle\Entity\IdTrait;
use Linderp\SuluBaseBundle\Entity\LocaleTrait;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOpt;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;

#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
class Newsletter
{
    use IdTrait;
    use LocaleTrait;

    final public const RESOURCE_KEY = 'newsletters';

    #[ORM\Column(type: 'uuid', nullable: false)]
    private ?string  $unsubscribePage;
    #[ORM\Column(type: 'uuid', nullable: false)]
    private ?string $doubleOptConfirmPage;
    /**
     * @var Collection<string, NewsletterTranslation>
     */
    #[ORM\OneToMany(mappedBy: 'newsletter', targetEntity: NewsletterTranslation::class, cascade: ['persist'], indexBy: 'locale')]
    private Collection $translations;

    /**
     * @var Collection<string, NewsletterSubscription>
     */
    #[ORM\OneToMany(mappedBy: 'newsletter', targetEntity: NewsletterSubscription::class, cascade: ['persist'])]
    private Collection $newsletterSubscriptions;
    /**
     * @var Collection<string, Newsletter>
     */
    #[ManyToMany(targetEntity: NewsletterMail::class, mappedBy: 'newsletters', cascade: ['persist'])]
    private Collection $newsletterMails;

    #[ORM\OneToOne(inversedBy: 'newsletter', targetEntity: NewsletterDoubleOpt::class, cascade: ['persist','remove'])]
    #[ORM\JoinColumn(name: 'double_opt_id', referencedColumnName: 'id',onDelete: 'CASCADE')]
    private NewsletterDoubleOpt $newsletterDoubleOpt;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->newsletterSubscriptions = new ArrayCollection();
        $this->newsletterMails = new ArrayCollection();
        $this->newsletterDoubleOpt = new NewsletterDoubleOpt();
    }

    /**
     * @return Collection
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * @return Collection
     */
    public function getNewsletterSubscriptions(): Collection
    {
        return $this->newsletterSubscriptions;
    }
    protected function getTranslation(string $locale): ?NewsletterTranslation
    {
        if (!$this->translations->containsKey($locale)) {
            return null;
        }

        return $this->translations->get($locale);
    }

    protected function createTranslation(string $locale): NewsletterTranslation
    {
        $translation = new NewsletterTranslation($this, $locale);
        $this->translations->set($locale, $translation);

        return $translation;
    }
    public function getTitle($locale = null): ?string
    {
        $translation = $this->getTranslation($locale ?? $this->locale);
        if (!$translation instanceof NewsletterTranslation) {
            return null;
        }

        return $translation->getTitle();
    }

    public function setTitle(string $title): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation instanceof NewsletterTranslation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setTitle($title);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getNewsletterMails(): Collection
    {
        return $this->newsletterMails;
    }

    /**
     * @param Collection $newsletterMails
     */
    public function setNewsletterMails(Collection $newsletterMails): void
    {
        $this->newsletterMails = $newsletterMails;
    }

    /**
     * @return NewsletterDoubleOpt
     */
    public function getNewsletterDoubleOpt(): NewsletterDoubleOpt
    {
        return $this->newsletterDoubleOpt;
    }

    /**
     * @param NewsletterDoubleOpt $newsletterDoubleOpt
     */
    public function setNewsletterDoubleOpt(NewsletterDoubleOpt $newsletterDoubleOpt): void
    {
        $this->newsletterDoubleOpt = $newsletterDoubleOpt;
    }
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        $this->newsletterDoubleOpt->setLocale($locale);
        return $this;
    }

    public function getUnsubscribePage(): ?string
    {
        return $this->unsubscribePage;
    }

    public function setUnsubscribePage(?string $unsubscribePage): void
    {
        $this->unsubscribePage = $unsubscribePage;
    }

    public function getDoubleOptConfirmPage(): ?string
    {
        return $this->doubleOptConfirmPage;
    }

    public function setDoubleOptConfirmPage(?string $doubleOptConfirmPage): void
    {
        $this->doubleOptConfirmPage = $doubleOptConfirmPage;
    }
}