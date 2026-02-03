<?php

namespace Linderp\SuluMailingListBundle\Entity\NewsletterMail;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailTranslationRepository;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: NewsletterMailTranslationRepository::class)]
class NewsletterMailTranslation extends MailTranslation implements AuditableInterface
{
    use AuditableTrait;
    public function __construct(
        #[ORM\ManyToOne(targetEntity: NewsletterMail::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private readonly NewsletterMail $newsletterMail,
        string $locale,
    ) {
        parent::__construct($locale);
    }
    /**
     * @return NewsletterMail
     */
    public function getNewsletterMail(): NewsletterMail
    {
        return $this->newsletterMail;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    public function copyTo(string $destLocale): static
    {
        $dest = new self($this->getNewsletterMail(), $destLocale);
        $dest->applyFrom($this);
        return $dest;
    }
}