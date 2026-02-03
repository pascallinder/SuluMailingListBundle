<?php

namespace Linderp\SuluMailingListBundle\Entity\NewsletterMail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Linderp\SuluBaseBundle\Entity\IdTrait;
use Linderp\SuluBaseBundle\Entity\LocaleTrait;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Sulu\Bundle\ContactBundle\Entity\Contact;


#[ORM\Entity(repositoryClass: NewsletterMailRepository::class)]
class NewsletterMail extends MailTranslatable
{
    final public const RESOURCE_KEY = 'newsletters_mails';

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $sent = false;
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sentAt;
    /**
     * @var Collection<string, Newsletter>
     */
    #[ORM\ManyToMany(targetEntity: Newsletter::class, inversedBy: 'newsletterMails', cascade: ['persist'])]
    #[JoinTable(name: 'newsletter_mails_mapping')]
    private Collection $newsletters;

    #[ORM\ManyToMany(targetEntity: Contact::class, inversedBy: 'newsletterMails', cascade: ['persist'])]
    #[JoinTable(name: 'newsletter_mail_contact_mapping')]
    private Collection $contacts;

    #[ORM\OneToMany(mappedBy: 'newsletterMail', targetEntity: NewsletterMailTranslation::class, cascade: ['persist'], indexBy: 'locale')]
    protected Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->newsletters = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }


    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
        $this->sentAt = new \DateTimeImmutable();
    }

    /**
     * @return \DateTimeInterface
     */
    public function getSentAt(): \DateTimeInterface
    {
        return $this->sentAt;
    }

    protected function createTranslation(string $locale): NewsletterMailTranslation
    {
        $translation = new NewsletterMailTranslation($this, $locale);
        $this->translations->set($locale, $translation);
        return $translation;
    }

    /**
     * @return Collection<Newsletter>
     */
    public function getNewsletters(): Collection
    {
        return $this->newsletters;
    }

    /**
     * @param Collection $newsletters
     */
    public function setNewsletters(Collection $newsletters): void
    {
        $this->newsletters = $newsletters;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function setTranslations(Collection $translations): void
    {
        $this->translations = $translations;
    }

    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function setContacts(Collection $contacts): void
    {
        $this->contacts = $contacts;
    }
    public function copy(): static
    {
        $dest = new self();
        $dest->applyFrom($this);
        $dest->setNewsletters($this->getNewsletters());
        $dest->setContacts($this->getContacts());
        return $dest;
    }

    protected function getTranslationClass(): string
    {
        return NewsletterMailTranslation::class;
    }
}