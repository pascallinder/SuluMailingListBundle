<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMailTranslation;
use Linderp\SuluMailingListBundle\Repository\MailTranslatableRepository;
use Linderp\SuluMailingListBundle\Repository\MailTranslationRepository;

class NewsletterMailTranslationRepository extends MailTranslationRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterMailTranslation::class);
    }


    protected function findOneByLocale(MailTranslatable $mailTranslatable, string $locale): ?NewsletterMailTranslation
    {
        /** @var ?NewsletterMailTranslation $result */
        $result =  $this->findOneBy([
            'locale' => $locale,
            'newsletterMail' => $mailTranslatable,
        ]);
        return $result;
    }
}