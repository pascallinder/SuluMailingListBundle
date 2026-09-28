<?php

namespace Linderp\SuluMailingListBundle\Preview;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Preview\Newsletter\MailTranslationPreviewObjectProvider;

/** @template T of MailTranslatable */
abstract readonly class MailTemplatePreviewObjectProvider extends MailTranslationPreviewObjectProvider
{
    /** @param LocaleRepositoryUtil<T> $repository */
    public function __construct(
        MailContextTypesPool $contextTypesPool,
        ManagerRegistry $managerRegistry,
        private LocaleRepositoryUtil $repository,
    ) {
        parent::__construct($contextTypesPool, $managerRegistry);
    }

    /** @return T|null */
    public function getObject(int|string $id, string $locale): ?MailTranslatable
    {
        $template = $this->repository->findById((int) $id, $locale);

        return $template instanceof MailTranslatable ? $template : null;
    }

    /** @param array<string, mixed> $data */
    public function setValues(object $object, string $locale, array $data): void
    {
        if (!$object instanceof MailTranslatable) {
            throw new \InvalidArgumentException('Expected a mail template preview object.');
        }

        $this->setMailTranslatableValues($object, $data);
    }
}
