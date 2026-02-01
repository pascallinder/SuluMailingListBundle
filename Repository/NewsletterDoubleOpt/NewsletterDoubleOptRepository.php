<?php

namespace Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\NewsletterDoubleOpt\NewsletterDoubleOpt;

/**
 * @extends LocaleRepositoryUtil<NewsletterDoubleOpt>
 */
class NewsletterDoubleOptRepository extends LocaleRepositoryUtil
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterDoubleOpt::class);
    }
    /**
     * @param array $options
     *
     * @return string[]
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