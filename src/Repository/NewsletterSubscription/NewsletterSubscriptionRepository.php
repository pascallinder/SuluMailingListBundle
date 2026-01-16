<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterSubscription;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;

/**
 * @extends LocaleRepositoryUtil<NewsletterSubscription>
 */
class NewsletterSubscriptionRepository extends LocaleRepositoryUtil
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterSubscription::class);
    }
    protected function append(QueryBuilder $queryBuilder, string $alias, string $locale, $options = []): array
    {
        return [];
    }

    protected function appendSortByJoins(QueryBuilder $queryBuilder, string $alias, string $locale): void
    {
    }
}