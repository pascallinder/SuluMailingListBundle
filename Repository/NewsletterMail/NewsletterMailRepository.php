<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterMail;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Repository\MailTranslatableRepository;

/**
 * @extends MailTranslatableRepository<NewsletterMail>
 */
class NewsletterMailRepository extends MailTranslatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct( $registry, NewsletterMail::class);
    }
    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    protected function append(QueryBuilder $queryBuilder, string $alias, string $locale, $options = []): array
    {
        return [];
    }

    protected function appendSortByJoins(QueryBuilder $queryBuilder, string $alias, string $locale): void
    {
        $queryBuilder->addSelect('translation')->innerJoin($alias . '.translations', 'translation', Join::WITH, 'translation.locale = :locale');
        $queryBuilder->setParameter('locale', $locale);
    }
}
