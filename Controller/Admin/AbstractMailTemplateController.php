<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Repository\LocaleRepositoryUtil;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\MailTranslation;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Repository\MailTranslationRepository;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template T of MailTranslatable
 * @template TT of MailTranslation
 * @extends MailTranslatableController<T>
 */
abstract class AbstractMailTemplateController extends MailTranslatableController
{
    /**
     * @param LocaleRepositoryUtil<T> $repository
     * @param MailTranslationRepository<TT> $translationRepository
     */
    public function __construct(
        protected readonly LocaleRepositoryUtil $repository,
        protected readonly MailTranslationRepository $translationRepository,
        protected readonly DoctrineListRepresentationFactory $listRepresentationFactory,
        MailContextTypesPool $mailContextTypes,
        MailContentProvider $mailContentProvider,
        ManagerRegistry $managerRegistry,
        #[Autowire('%sulu_mailing_list.no_reply_email%')]
        string $noReplyEmail,
    ) {
        parent::__construct($mailContextTypes, $noReplyEmail, $mailContentProvider, $managerRegistry, $repository);
    }

    abstract protected function getResourceKey(): string;

    /** @param T $entity */
    protected function getDataForEntity($entity, Request $request): array
    {
        return $this->getDataForMailTranslatable($entity, ['id' => $entity->getId()]);
    }

    /** @param T $entity @param array<string, mixed> $data */
    protected function mapDataToEntity(array $data, $entity, Request $request): void
    {
        $entity->setSubject(is_string($data['subject'] ?? null) ? $data['subject'] : '');
        $this->mapDataToMailTranslatable($entity, $data);
    }

    protected function getTemplateList(Request $request): Response
    {
        $list = $this->listRepresentationFactory->createDoctrineListRepresentation(
            $this->getResourceKey(),
            [],
            ['locale' => $this->getLocale($request)],
        );

        return $this->json($list->toArray());
    }

    protected function triggerSwitch(Request $request, string $action, $entity): void
    {
        if ('copy_locale' !== $action) {
            return;
        }

        $dest = $request->query->all()['dest'] ?? [];
        $destLocales = array_values(array_filter(
            is_array($dest) ? $dest : [$dest],
            static fn(mixed $locale): bool => is_string($locale),
        ));

        $this->translationRepository->copyLocale(
            $entity,
            $request->query->getString('locale'),
            $destLocales,
        );
    }

    public function indexAction(MailTranslatable $object): Response
    {
        return $this->getIndexResponse($object);
    }
}
