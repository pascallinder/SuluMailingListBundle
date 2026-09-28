<?php

namespace Linderp\SuluMailingListBundle\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluBaseBundle\Common\DoctrineListRepresentationFactory;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeaderTranslation;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Repository\MailTemplateHeader\MailTemplateHeaderRepository;
use Linderp\SuluMailingListBundle\Repository\MailTemplateHeader\MailTemplateHeaderTranslationRepository;
use Linderp\SuluMailingListBundle\Service\Mail\MailContentProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** @extends AbstractMailTemplateController<MailTemplateHeader, MailTemplateHeaderTranslation> */
class MailTemplateHeaderController extends AbstractMailTemplateController
{
    public function __construct(
        MailTemplateHeaderRepository $repository,
        MailTemplateHeaderTranslationRepository $translationRepository,
        DoctrineListRepresentationFactory $listRepresentationFactory,
        MailContextTypesPool $mailContextTypes,
        MailContentProvider $mailContentProvider,
        ManagerRegistry $managerRegistry,
        #[Autowire('%sulu_mailing_list.no_reply_email%')]
        string $noReplyEmail,
    ) {
        parent::__construct($repository, $translationRepository, $listRepresentationFactory, $mailContextTypes, $mailContentProvider, $managerRegistry, $noReplyEmail);
    }

    #[Route('/admin/api/mail-template-headers/{id}', name: 'app.get_mail_template_header', methods: ['GET'])]
    public function getAction(int $id, Request $request): Response
    {
        return $this->handleGetByIdRequest($id, $request);
    }

    #[Route('/admin/api/mail-template-headers/{id}', name: 'app.put_mail_template_header', methods: ['PUT'])]
    public function putAction(int $id, Request $request): Response
    {
        return $this->handlePutRequest($id, $request);
    }

    #[Route('/admin/api/mail-template-headers', name: 'app.post_mail_template_header', methods: ['POST'])]
    public function postAction(Request $request): Response
    {
        return $this->handlePostRequest($request);
    }

    #[Route('/admin/api/mail-template-headers/{id}', name: 'app.post_mail_template_header_trigger', methods: ['POST'])]
    public function postTriggerAction(int $id, Request $request): Response
    {
        return $this->handlePostTriggerRequest($id, $request);
    }

    #[Route('/admin/api/mail-template-headers/{id}', name: 'app.delete_mail_template_header', methods: ['DELETE'])]
    public function deleteAction(int $id): Response
    {
        return $this->handleDeleteRequest($id);
    }

    #[Route('/admin/api/mail-template-headers', name: 'app.get_mail_template_header_list', methods: ['GET'])]
    public function getListAction(Request $request): Response
    {
        return $this->getTemplateList($request);
    }

    protected function getResourceKey(): string
    {
        return MailTemplateHeader::RESOURCE_KEY;
    }
}
