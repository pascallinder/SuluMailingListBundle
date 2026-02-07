<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluMailingListBundle\Entity\MailTranslatable;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt\NewsletterDoubleOptRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterDoubleOpt\NewsletterDoubleOptTranslationRepository;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @phpstan-type NewsletterData array{
 *     id: int|null,
 *     enabled: bool,
 *     title: string,
 *     category: array{id: int}|null,
 * }
 * @extends LocaleController<Newsletter>
 */
class NewsletterController extends MailTranslatableController
{
    public function __construct(

        private readonly NewsletterRepository $newsletterRepository,
        private readonly NewsletterDoubleOptTranslationRepository $newsletterDoubleOptTranslationRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        MailContextTypesPool $mailContextTypes,
        MailContentProvider $mailContentProvider,
        #[Autowire('%sulu_mailing_list.no_reply_email%')]
        string $noReplyEmail,
    )
    {
        parent::__construct($mailContextTypes,$noReplyEmail,$mailContentProvider,$this->newsletterRepository);
    }
    #[Route(path: '/admin/api/newsletters/{id}', name: 'app.get_newsletter', methods: ['GET'])]
    public function getAction(int $id, Request $request): Response
    {
        return $this->handleGetByIdRequest($id,$request);
    }

    #[Route(path: '/admin/api/newsletters/{id}', name: 'app.put_newsletter', methods: ['PUT'])]
    public function putAction(int $id, Request $request): Response
    {
        return $this->handlePutRequest($id,$request);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/admin/api/newsletters', name: 'app.post_newsletter', methods: ['POST'])]
    public function postAction(Request $request): Response
    {
        return $this->handlePostRequest($request);
    }

    #[Route(path: '/admin/api/newsletters/{id}', name: 'app.post_newsletter_trigger', methods: ['POST'])]
    public function postTriggerAction(int $id, Request $request): Response
    {
        return $this->handlePostTriggerRequest($id,$request);
    }

    #[Route(path: '/admin/api/newsletters/{id}', name: 'app.delete_newsletter', methods: ['DELETE'])]
    public function deleteAction(int $id): Response
    {
        return $this->handleDeleteRequest($id);
    }

    #[Route(path: '/admin/api/newsletters', name: 'app.get_newsletter_list', methods: ['GET'])]
    public function getListAction(Request $request): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Newsletter::RESOURCE_KEY,
            [],
            ['locale' => $this->getLocale($request)],
        );

        return $this->json($listRepresentation->toArray());
    }
    /**
     * @param Newsletter $entity
     * @return NewsletterData
     */
    protected function getDataForEntity($entity, Request $request): array
    {
        $doubleOpt = $entity->getNewsletterDoubleOpt();
        $data = [
            'id' => $entity->getId(),
            'title' => $entity->getTitle() ?? '',
            'doubleOptConfirmPage' => $entity->getDoubleOptConfirmPage(),
            'unsubscribePage' => $entity->getUnsubscribePage(),
        ];
        return $this->getDataForMailTranslatable($doubleOpt,$data);
    }
    /**
     * @param Newsletter $entity
     */
    protected function mapDataToEntity(array $data, $entity, Request $request): void
    {
        $doubleOpt = $entity->getNewsletterDoubleOpt();
        $entity->setTitle($data['title'] );
        $entity->setDoubleOptConfirmPage($data['doubleOptConfirmPage']);
        $entity->setUnsubscribePage($data['unsubscribePage']);
        $doubleOpt->setSubject($data['subject'] ?? '');
        $this->mapDataToMailTranslatable($doubleOpt,$data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function indexAction(Newsletter $newsletter): Response
    {
        return $this->getIndexResponse($newsletter->getNewsletterDoubleOpt(),['doubleOptUrl' => 'https://google.ch']);
    }

    protected function triggerSwitch(Request $request, string $action, $entity): void
    {
        switch ($action) {
            case 'copy-locale':{
                $this->newsletterDoubleOptTranslationRepository->copyLocale($entity->getNewsletterDoubleOpt(),
                    $request->query->get('locale'),
                    $request->query->get('dest'));
                break;
            }
        }
    }
}