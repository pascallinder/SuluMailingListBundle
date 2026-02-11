<?php

namespace Linderp\SuluMailingListBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of MailTranslation
 * @extends ServiceEntityRepository<T>
 */
abstract class MailTranslationRepository extends ServiceEntityRepository
{
    /**
     * @param class-string<T> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }
    public function copyLocale(MailTranslatable $mailTranslatable, string $srcLocale, string $destLocale): void
    {
        $src = $this->findOneByLocale($mailTranslatable, $srcLocale);
        $dest = $this->findOneByLocale($mailTranslatable, $destLocale);
        if($dest !== null){
            $dest->applyFrom($src);
        }else{
            $dest = $src->copyTo($destLocale);
        }
        $this->getEntityManager()->persist($dest);
        $this->getEntityManager()->flush();
    }
    /**
     * @return T|null
     */
    protected abstract function findOneByLocale(MailTranslatable $mailTranslatable, string $locale): ?MailTranslation;
}
