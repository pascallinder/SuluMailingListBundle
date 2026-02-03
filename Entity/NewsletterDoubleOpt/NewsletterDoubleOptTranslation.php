<?php

namespace Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailTranslationRepository;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: NewsletterMailTranslationRepository::class)]
class NewsletterDoubleOptTranslation extends MailTranslation implements AuditableInterface
{
    use AuditableTrait;
    public function __construct(
        #[ORM\ManyToOne(targetEntity: NewsletterDoubleOpt::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private readonly NewsletterDoubleOpt $newsletterDoubleOpt,
        string $locale,
    ) {
        parent::__construct($locale);
    }
    /**
     * @return NewsletterDoubleOpt
     */
    public function getNewsletterDoubleOpt(): NewsletterDoubleOpt
    {
        return $this->newsletterDoubleOpt;
    }
    public function copyTo(string $destLocale): static
    {
        $dest = new self($this->getNewsletterDoubleOpt(), $destLocale);
        $dest->applyFrom($this);
        return $dest;
    }
}