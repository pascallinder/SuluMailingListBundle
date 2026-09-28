<?php

namespace Linderp\SuluMailingListBundle\Repository\MailTemplateFooter;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Repository\MailTranslatableRepository;

/** @extends MailTranslatableRepository<MailTemplateFooter> */
class MailTemplateFooterRepository extends MailTranslatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailTemplateFooter::class);
    }

    /** @return list<string> */
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
