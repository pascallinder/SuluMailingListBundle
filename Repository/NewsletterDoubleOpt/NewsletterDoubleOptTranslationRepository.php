<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOptTranslation;
use Linderp\SuluMailingListBundle\Repository\MailTranslatableRepository;
use Linderp\SuluMailingListBundle\Repository\MailTranslationRepository;

/**
 * @extends ServiceEntityRepository<NewsletterDoubleOptTranslation>
 */
class NewsletterDoubleOptTranslationRepository extends MailTranslationRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterDoubleOptTranslation::class);
    }

    protected function findOneByLocale(MailTranslatable $mailTranslatable, string $locale): ?MailTranslation
    {
        /** @var ?MailTranslation $result */
        $result =  $this->findOneBy([
            'locale' => $locale,
            'newsletterDoubleOpt' => $mailTranslatable,
        ]);
        return $result;
    }
}