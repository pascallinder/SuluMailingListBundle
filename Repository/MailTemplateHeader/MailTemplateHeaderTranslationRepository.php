<?php

namespace Linderp\SuluMailingListBundle\Repository\MailTemplateHeader;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeaderTranslation;
use Linderp\SuluMailingListBundle\Repository\MailTranslationRepository;

/** @extends MailTranslationRepository<MailTemplateHeaderTranslation> */
class MailTemplateHeaderTranslationRepository extends MailTranslationRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailTemplateHeaderTranslation::class);
    }

    protected function findOneByLocale(MailTranslatable $mailTranslatable, string $locale): ?MailTemplateHeaderTranslation
    {
        return $this->findOneBy(['locale' => $locale, 'header' => $mailTranslatable]);
    }
}
