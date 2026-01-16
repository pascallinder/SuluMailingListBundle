<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
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
        private readonly MediaManagerInterface $mediaManager
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
            'doubleOpt_title' => $doubleOpt->getTitle(),
            'doubleOpt_body' => $doubleOpt->getBody(),
            'doubleOpt_senderMail' => $doubleOpt->getSenderMail(),
            'doubleOpt_headerImage' => $doubleOpt->getHeaderImage()
                ? ['id' => $doubleOpt->getHeaderImage()->getId()]
                : null,
            'doubleOpt_mailTemplate' => $doubleOpt->getMailTemplate(),
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
        $doubleOpt->setTitle($data['doubleOpt_title'] ?? '');
        $doubleOpt->setSubject($data['doubleOpt_subject'] ?? '');
        $doubleOpt->setBody($data['doubleOpt_body'] ?? '');
        $doubleOpt->setSenderMail($data['doubleOpt_senderMail'] ?? '');
        $doubleOpt->setMailTemplate($data['doubleOpt_mailTemplate'] ?? '');
        if(array_key_exists('doubleOpt_headerImage',$data)){
            $doubleOpt->setHeaderImage($data['doubleOpt_headerImage'] && $data['doubleOpt_headerImage']['id'] ?
                $this->mediaManager->getEntityById($data['doubleOpt_headerImage']['id']): null);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function indexAction(Newsletter $newsletter): Response
    {
        return new Response('<!-- CONTENT-REPLACER -->'.$this->mailContentProvider->getMailTranslatableMailContent($newsletter->getNewsletterDoubleOpt(),$newsletter->getLocale(), [
                'firstName' => 'Max',
                'lastName' => 'Mustermann',
                'unsubscribeUrl' => 'https://refashion.ch',
                'doubleOptUrl'=>'https://refashion.ch'
                ]
            ).'<!-- CONTENT-REPLACER -->');
    }

    protected function triggerSwitch(Request $request, string $action, $entity): void
    {
        return;
    }
}