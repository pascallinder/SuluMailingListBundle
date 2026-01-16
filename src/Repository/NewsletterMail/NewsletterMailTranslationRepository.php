<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMailTranslation;

/**
 * @extends ServiceEntityRepository<NewsletterMailTranslation>
 */
class NewsletterMailTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterMailTranslation::class);
    }
}