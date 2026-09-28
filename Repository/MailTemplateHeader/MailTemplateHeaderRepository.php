<?php

namespace Linderp\SuluMailingListBundle\Repository\MailTemplateHeader;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Repository\MailTranslatableRepository;

/** @extends MailTranslatableRepository<MailTemplateHeader> */
class MailTemplateHeaderRepository extends MailTranslatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailTemplateHeader::class);
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
