<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Attribute\Route;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionMailService;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class NewsletterMailController extends LocaleController
{
    public function __construct(
        private readonly NewsletterMailRepository          $newsletterMailRepository,
        private readonly NewsletterRepository              $newsletterRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly MailContentProvider               $subscriptionMailProvider,
        private readonly SubscriptionMailService           $subscriptionMailService,
        protected readonly WebspaceManagerInterface        $webspaceManager,
        private readonly MediaManagerInterface             $mediaManager
    )
    {
        parent::__construct($this->newsletterMailRepository);
    }
    #[Route(path: '/admin/api/newsletters-mails/{id}', name: 'app.get_newsletter_mail', methods: ['GET'])]
    public function getAction(int $id, Request $request): Response
    {
        return $this->handleGetByIdRequest($id,$request);
    }

    #[Route(path: '/admin/api/newsletters-mails/{id}', name: 'app.put_newsletter_mail', methods: ['PUT'])]
    public function putAction(int $id, Request $request): Response
    {
        return $this->handlePutRequest($id,$request);
    }

    #[Route(path: '/admin/api/newsletters-mails', name: 'app.post_newsletter_mail', methods: ['POST'])]
    public function postAction(Request $request): Response
    {
        return $this->handlePostRequest($request);
    }

    #[Route(path: '/admin/api/newsletters-mails/{id}', name: 'app.post_newsletter_mail_trigger', methods: ['POST'])]
    public function postTriggerAction(int $id, Request $request): Response
    {
        return $this->handlePostTriggerRequest($id,$request);
    }

    #[Route(path: '/admin/api/newsletters-mails/{id}', name: 'app.delete_newsletter_mail', methods: ['DELETE'])]
    public function deleteAction(int $id): Response
    {
        return $this->handleDeleteRequest($id);
    }
    #[Route(path: '/admin/api/newsletters-mails', name: 'app.get_newsletter_mail_list', methods: ['GET'])]
    public function getListAction(Request $request): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            NewsletterMail::RESOURCE_KEY,
            [],
            ['locale' => $this->getLocale($request)],
        );

        return $this->json($listRepresentation->toArray());
    }

    /**
     * @param NewsletterMail $entity
     */
    protected function getDataForEntity($entity, Request $request): array
    {
          return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle() ?? '',
            'subject' => $entity->getSubject(),
            'mailTemplate' => $entity->getMailTemplate(),
            'newsletters'=>array_map(fn(Newsletter $newsletter)=> $newsletter->getId(),
                $entity->getNewsletters()->getValues()),
            'senderMail'=>$entity->getSenderMail(),
            'body'=> $entity->getBody(),
            'sent' => $entity->isSent(),
            'headerImage' => $entity->getHeaderImage() ? ["id"=>$entity->getHeaderImage()->getId()] : null,
            'readyForSend'=> count(array_filter($this->webspaceManager->getAllLocales(),fn($locale)=>!$entity->hasTranslation($locale))) === 0
        ];
    }

    /**
     * @param NewsletterMail $entity
     */
    protected function mapDataToEntity(array $data,$entity, Request $request): void
    {
        $entity->setTitle($data['title']);
        $entity->setNewsletters(new ArrayCollection(
            $this->newsletterRepository->findBy(['id'=>$data['newsletters']])));
        $entity->setBody($data['body']);
        $entity->setSubject($data['subject']);
        $entity->setMailTemplate($data['mailTemplate']);
        $entity->setSenderMail($data['senderMail']);
        $entity->setHeaderImage($data['headerImage'] && $data['headerImage']['id'] ?
            $this->mediaManager->getEntityById($data['headerImage']['id']): null);
    }

    /**
     * @param Request $request
     * @param string $action
     * @param NewsletterMail $entity
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     */
    protected function triggerSwitch(Request $request, string $action, $entity): void
    {
        if ($action == 'send') {
            $this->subscriptionMailService->sendMailToSubscribers($entity);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function indexAction(NewsletterMail $mail): Response
    {
        return new Response('<!-- CONTENT-REPLACER -->'.$this->subscriptionMailProvider->getMailTranslatableMailContent($mail,$mail->getLocale(),[
            'firstName' => 'Max',
            'lastName' => 'Mustermann',
            'unsubscribeUrl' => 'https://refashion.ch']
        ).'<!-- CONTENT-REPLACER -->');
    }
}