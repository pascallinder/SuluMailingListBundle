<?php

namespace Linderp\SuluMailingListBundle\Entity\MailTemplateFooter;

use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Repository\MailTemplateFooter\MailTemplateFooterTranslationRepository;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: MailTemplateFooterTranslationRepository::class)]
class MailTemplateFooterTranslation extends MailTranslation implements AuditableInterface
{
    use AuditableTrait;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: MailTemplateFooter::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private readonly MailTemplateFooter $footer,
        string $locale,
    ) {
        parent::__construct($locale);
    }

    public function getFooter(): MailTemplateFooter
    {
        return $this->footer;
    }

    public function copyTo(string $destLocale): self
    {
        $copy = new self($this->footer, $destLocale);
        $copy->applyFrom($this);

        return $copy;
    }
}
