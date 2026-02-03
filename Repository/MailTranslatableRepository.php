<?php

namespace Linderp\SuluMailingListBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;

abstract class MailTranslatableRepository extends LocaleRepositoryUtil
{
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