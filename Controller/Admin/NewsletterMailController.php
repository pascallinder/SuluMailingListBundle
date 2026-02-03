<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluBaseBundle\Controller\Admin\LocaleController;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMailTranslation;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailTranslationRepository;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionMailService;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\ContactBundle\Entity\ContactRepositoryInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewsletterMailController extends LocaleController
{
    public function __construct(
        private readonly NewsletterMailRepository          $newsletterMailRepository,
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly NewsletterMailTranslationRepository $newsletterMailTranslationRepository,
        private readonly NewsletterRepository              $newsletterRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly MailContentProvider               $subscriptionMailProvider,
        private readonly SubscriptionMailService           $subscriptionMailService,
        protected readonly WebspaceManagerInterface        $webspaceManager,
        #[Autowire('%sulu_mailing_list.no_reply_email%')]
        private readonly string                            $noReplyEmail,
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
            'content' => $entity->getContent() ?? [],
            'subject' => $entity->getSubject(),
            'newsletters'=>array_map(fn(Newsletter $newsletter)=> $newsletter->getId(),
                $entity->getNewsletters()->getValues()),
            'contacts'=>array_map(fn(Contact $contact)=> $contact->getId(),
              $entity->getContacts()->getValues()),
            'senderMail'=>$entity->getSenderMail(),
            'sent' => $entity->isSent(),
            'readyForSend'=> count(array_filter($this->webspaceManager->getAllLocales(),fn($locale)=>!$entity->hasTranslation($locale))) === 0
        ];
    }

    /**
     * @param NewsletterMail $entity
     */
    protected function mapDataToEntity(array $data,$entity, Request $request): void
    {
        $entity->setContent($data['content']);
        $entity->setNewsletters(new ArrayCollection(
            $this->newsletterRepository->findBy(['id'=>$data['newsletters']])));
        $entity->setSubject($data['subject']);
        $entity->setContacts(new ArrayCollection(
            $this->contactRepository->findBy(['id'=>$data['contacts']])));
        $entity->setSenderMail($data['senderMail'] ?? $this->noReplyEmail);
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
        switch ($action) {
            case 'send':{
                $this->subscriptionMailService->sendMailToSubscribers($entity);
                break;
            }
            case 'copy-locale':{
                $this->newsletterMailTranslationRepository->copyLocale($entity,
                    $request->query->get('locale'),
                    $request->query->get('dest'));
                break;
            }
            case 'copy':{
                $this->newsletterMailRepository->copy($entity);
            }
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
            'unsubscribeUrl' => 'https://google.ch']
        ).'<!-- CONTENT-REPLACER -->');
    }
}