<?php

namespace Linderp\SuluMailingListBundle\Preview\MailTemplateFooter;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Controller\Admin\MailTemplateFooterController;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Preview\MailTemplatePreviewObjectProvider;
use Linderp\SuluMailingListBundle\Repository\MailTemplateFooter\MailTemplateFooterRepository;

/** @extends MailTemplatePreviewObjectProvider<MailTemplateFooter> */
readonly class MailTemplateFooterPreviewObjectProvider extends MailTemplatePreviewObjectProvider
{
    public function __construct(
        MailContextTypesPool $contextTypesPool,
        ManagerRegistry $managerRegistry,
        MailTemplateFooterRepository $repository,
    )
    {
        parent::__construct($contextTypesPool, $managerRegistry, $repository);
    }

    public function getPreviewController(): string
    {
        return MailTemplateFooterController::class . '::indexAction';
    }
}
