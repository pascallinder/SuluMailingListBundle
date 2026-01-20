<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
/**
 * @extends ServiceEntityRepository<NewsletterSubscription>
 */
class NewsletterSubscriptionRepository extends ServiceEntityRepository
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
    public function save(NewsletterSubscription $subscription): void
    {
        $this->getEntityManager()->persist($subscription);
    }
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}