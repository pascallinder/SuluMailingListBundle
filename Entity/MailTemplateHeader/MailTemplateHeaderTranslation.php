<?php

namespace Linderp\SuluMailingListBundle\Entity\MailTemplateHeader;

use Doctrine\ORM\Mapping as ORM;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Repository\MailTemplateHeader\MailTemplateHeaderTranslationRepository;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

#[ORM\Entity(repositoryClass: MailTemplateHeaderTranslationRepository::class)]
class MailTemplateHeaderTranslation extends MailTranslation implements AuditableInterface
{
    use AuditableTrait;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: MailTemplateHeader::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private readonly MailTemplateHeader $header,
        string $locale,
    ) {
        parent::__construct($locale);
    }

    public function getHeader(): MailTemplateHeader
    {
        return $this->header;
    }

    public function copyTo(string $destLocale): self
    {
        $copy = new self($this->header, $destLocale);
        $copy->applyFrom($this);

        return $copy;
    }
}
