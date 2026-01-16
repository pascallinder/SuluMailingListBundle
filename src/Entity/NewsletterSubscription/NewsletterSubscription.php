<?php

namespace Linderp\SuluMailingListBundle\Entity\NewsletterSubscription;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluBaseBundle\Entity\IdTrait;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\NewsletterSubscription\NewsletterSubscriptionRepository;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\ContactBundle\Entity\ContactInterface;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: NewsletterSubscriptionRepository::class)]
class NewsletterSubscription
{
    use IdTrait;
    final public const RESOURCE_KEY = 'newsletters_subscriptions';
    #[ORM\Column(type: 'datetime' , nullable: false)]
    private \DateTimeInterface $subscribedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $unsubscribedAt = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $confirmationToken;

    #[ORM\Column(type: 'boolean')]
    private bool $isConfirmed = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $confirmedAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isUnsubscribed = false;

    #[ORM\Column(type: 'string', length: 255)]
    private string $unsubscribeToken;

    /**
     * @throws \Exception
     */
    #[ORM\PrePersist]
    public function setSubscribed(): void
    {
        $this->subscribedAt = new \DateTimeImmutable();
        $this->confirmationToken = bin2hex(random_bytes(32));
        $this->unsubscribeToken = bin2hex(random_bytes(32));
        $this->isConfirmed = false;
        $this->confirmedAt = null;
        $this->unsubscribedAt = null;
        $this->isUnsubscribed = false;
    }
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Newsletter::class, inversedBy: 'newsletterSubscriptions')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private readonly Newsletter $newsletter,
        #[ORM\ManyToOne(targetEntity: Contact::class)]
        #[ORM\JoinColumn(referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly ContactInterface $contact,
        #[ORM\Column(type: Types::STRING, length: 5, nullable: false)]
        private readonly string $locale,
    ) {
    }
    /**
     * @return \DateTimeInterface
     */
    public function getSubscribedAt(): \DateTimeInterface
    {
        return $this->subscribedAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUnsubscribedAt(): ?\DateTimeInterface
    {
        return $this->unsubscribedAt;
    }

    /**
     * @return string
     */
    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }
    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(): void
    {
        $this->isConfirmed = true;
        $this->confirmedAt = new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getUnsubscribeToken(): string
    {
        return $this->unsubscribeToken;
    }
    /**
     * @return Newsletter
     */
    public function getNewsletter(): Newsletter
    {
        return $this->newsletter;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @return bool
     */
    public function isUnsubscribed(): bool
    {
        return $this->isUnsubscribed;
    }

    public function setUnsubscribed(): void
    {
        $this->isUnsubscribed = true;
        $this->unsubscribedAt = new \DateTimeImmutable();
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}