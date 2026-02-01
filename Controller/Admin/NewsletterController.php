<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
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
class NewsletterController extends LocaleController
{
    public function __construct(
        private readonly NewsletterRepository $newsletterRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly MailContentProvider $mailContentProvider,
        #[Autowire('%sulu_mailing_list.no_reply_email%')]
        private readonly string $noReplyEmail,
    )
    {
        parent::__construct($this->newsletterRepository);
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
        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle() ?? '',
            'doubleOptConfirmPage' => $entity->getDoubleOptConfirmPage(),
            'unsubscribePage' => $entity->getUnsubscribePage(),
            'doubleOpt_subject' => $doubleOpt->getSubject(),
            'content' => $doubleOpt->getContent(),
            'doubleOpt_senderMail' => $doubleOpt->getSenderMail(),
        ];
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
        $doubleOpt->setContent($data['content'] ?? '');
        $doubleOpt->setSubject($data['doubleOpt_subject'] ?? '');
        $doubleOpt->setSenderMail($data['doubleOpt_senderMail'] ?? $this->noReplyEmail);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function indexAction(Newsletter $newsletter): Response
    {
        return new Response('<!-- CONTENT-REPLACER -->'.$this->mailContentProvider->getMailTranslatableMailContent($newsletter->getNewsletterDoubleOpt(),$newsletter->getLocale(), [
                'firstName' => 'Max',
                'lastName' => 'Mustermann',
                'unsubscribeUrl' => 'https://google.ch',
                'doubleOptUrl'=>'https://google.ch'
                ]
            ).'<!-- CONTENT-REPLACER -->');
    }

    protected function triggerSwitch(Request $request, string $action, $entity): void
    {
        return;
    }
}