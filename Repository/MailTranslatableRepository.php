<?php

namespace Linderp\SuluMailingListBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;

/**
 * @template T of MailTranslatable
 * @extends LocaleRepositoryUtil<T>
 */
abstract class MailTranslatableRepository extends LocaleRepositoryUtil
{
    /**
     * @param class-string<T> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }
    public function copy(MailTranslatable $mailTranslatable):void{
        $copy = $mailTranslatable->copy();
        $this->getEntityManager()->persist($copy);
        $this->getEntityManager()->flush();
    }
}
