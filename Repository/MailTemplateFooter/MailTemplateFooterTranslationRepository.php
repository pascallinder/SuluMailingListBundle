<?php

namespace Linderp\SuluMailingListBundle\Repository\MailTemplateFooter;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooterTranslation;
use Linderp\SuluMailingListBundle\Repository\MailTranslationRepository;

/** @extends MailTranslationRepository<MailTemplateFooterTranslation> */
class MailTemplateFooterTranslationRepository extends MailTranslationRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailTemplateFooterTranslation::class);
    }

    protected function findOneByLocale(MailTranslatable $mailTranslatable, string $locale): ?MailTemplateFooterTranslation
    {
        return $this->findOneBy(['locale' => $locale, 'footer' => $mailTranslatable]);
    }
}
