<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOptTranslation;

/**
 * @extends ServiceEntityRepository<NewsletterDoubleOptTranslation>
 */
class NewsletterDoubleOptTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterDoubleOptTranslation::class);
    }
}