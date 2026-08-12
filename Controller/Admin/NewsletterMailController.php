<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Repository\Newsletter\NewsletterRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailRepository;
use Linderp\SuluMailingListBundle\Repository\NewsletterMail\NewsletterMailTranslationRepository;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Linderp\SuluMailingListBundle\Service\Mail\Mailer;
use Linderp\SuluMailingListBundle\Service\Subscription\SubscriptionMailService;
use Psr\Cache\InvalidArgumentException;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\ContactBundle\Entity\ContactRepositoryInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @extends MailTranslatableController<NewsletterMail>
 */
class NewsletterMailController extends MailTranslatableController
{
    public function __construct(
        private readonly NewsletterMailRepository          $newsletterMailRepository,
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly NewsletterMailTranslationRepository $newsletterMailTranslationRepository,
        private readonly NewsletterRepository              $newsletterRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly SubscriptionMailService           $subscriptionMailService,
        private readonly Mailer                            $mailer,
        protected readonly WebspaceManagerInterface        $webspaceManager,
        MailContentProvider  $mailContentProvider,
        MailContextTypesPool $mailContextTypes,
        #[Autowire('%sulu_mailing_list.no_reply_email%')]
        string $noReplyEmail,
    ) {
        parent::__construct(
            $mailContextTypes,
            $noReplyEmail,
            $mailContentProvider,
            $this->newsletterMailRepository
        );
    }
    #[Route(path: '/admin/api/newsletters-mails/{id}', name: 'app.get_newsletter_mail', methods: ['GET'])]
    public function getAction(int $id, Request $request): Response
    {
        return $this->handleGetByIdRequest($id, $request);
    }

    #[Route(path: '/admin/api/newsletters-mails/{id}', name: 'app.put_newsletter_mail', methods: ['PUT'])]
    public function putAction(int $id, Request $request): Response
    {
        return $this->handlePutRequest($id, $request);
    }

    #[Route(path: '/admin/api/newsletters-mails', name: 'app.post_newsletter_mail', methods: ['POST'])]
    public function postAction(Request $request): Response
    {
        return $this->handlePostRequest($request);
    }

    #[Route(path: '/admin/api/newsletters-mails/test', name: 'app.post_newsletter_mail_test', methods: ['POST'], priority: 10)]
    public function postTestAction(Request $request): Response
    {
        $payload = $request->toArray();
        $id = filter_var($payload['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $recipient = $payload['recipient'] ?? null;
        $locale = $payload['locale'] ?? null;

        if (false === $id) {
            throw new BadRequestHttpException('A saved newsletter ID is required.');
        }
        if (!is_string($recipient) || false === filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException('A valid test recipient email address is required.');
        }
        if (!is_string($locale) || !in_array($locale, $this->webspaceManager->getAllLocales(), true)) {
            throw new BadRequestHttpException('A valid locale is required.');
        }

        $newsletterMail = $this->newsletterMailRepository->find($id);
        if (!$newsletterMail instanceof NewsletterMail) {
            throw new NotFoundHttpException('Newsletter mail not found.');
        }

        $newsletterMail->setLocale($locale);
        $this->mailer->sendMails($this->mailer->prepareTestMail($newsletterMail, $recipient, $locale));

        return $this->json(['sent' => true]);
    }

    #[Route(path: '/admin/api/newsletters-mails/{id}', name: 'app.post_newsletter_mail_trigger', methods: ['POST'])]
    public function postTriggerAction(int $id, Request $request): Response
    {
        return $this->handlePostTriggerRequest($id, $request);
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
     * @return array<string, mixed>
     */
    protected function getDataForEntity($entity, Request $request): array
    {
        $data = [
            'id' => $entity->getId(),
            'newsletters' => array_map(
                fn(Newsletter $newsletter) => $newsletter->getId(),
                $entity->getNewsletters()->getValues()
            ),
            'contacts' => array_map(
                fn(Contact $contact) => $contact->getId(),
                $entity->getContacts()->getValues()
            ),
            'sent' => $entity->isSent(),
            'readyForSend' => count(array_filter($this->webspaceManager->getAllLocales(), fn($locale) => !$entity->hasTranslation($locale))) === 0,
        ];
        return $this->getDataForMailTranslatable($entity, $data);
    }

    /**
     * @param NewsletterMail $entity
     * @param array<string, mixed> $data
     */
    protected function mapDataToEntity(array $data, $entity, Request $request): void
    {

        $newsletters = array_filter(
            $this->newsletterRepository->findBy(['id' => $data['newsletters']]),
            static fn($newsletter): bool => $newsletter instanceof Newsletter,
        );
        $entity->setNewsletters(new ArrayCollection($newsletters));
        $entity->setSubject($data['subject']);
        $contacts = array_filter(
            $this->contactRepository->findBy(['id' => $data['contacts']]),
            static fn($contact): bool => $contact instanceof Contact,
        );
        $entity->setContacts(new ArrayCollection($contacts));
        $this->mapDataToMailTranslatable($entity, $data);
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
                $this->newsletterMailTranslationRepository->copyLocale(
                    $entity,
                    $request->query->get('locale'),
                    $request->query->get('dest')
                );
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
    public function indexAction(NewsletterMail $object): Response
    {
        return $this->getIndexResponse($object);
    }
}
