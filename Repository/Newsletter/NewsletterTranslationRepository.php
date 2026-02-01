<?php

namespace Linderp\SuluMailingListBundle\Repository\Newsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\Newsletter\NewsletterTranslation;

/**
 * @extends ServiceEntityRepository<NewsletterTranslation>
 */
class NewsletterTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterTranslation::class);
    }
}