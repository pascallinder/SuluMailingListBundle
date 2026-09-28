<?php

namespace Linderp\SuluMailingListBundle\Preview\MailTemplateHeader;

use Doctrine\Persistence\ManagerRegistry;
use Linderp\SuluMailingListBundle\Controller\Admin\MailTemplateHeaderController;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;
use Linderp\SuluMailingListBundle\Mail\Context\MailContextTypesPool;
use Linderp\SuluMailingListBundle\Preview\MailTemplatePreviewObjectProvider;
use Linderp\SuluMailingListBundle\Repository\MailTemplateHeader\MailTemplateHeaderRepository;

/** @extends MailTemplatePreviewObjectProvider<MailTemplateHeader> */
readonly class MailTemplateHeaderPreviewObjectProvider extends MailTemplatePreviewObjectProvider
{
    public function __construct(
        MailContextTypesPool $contextTypesPool,
        ManagerRegistry $managerRegistry,
        MailTemplateHeaderRepository $repository,
    )
    {
        parent::__construct($contextTypesPool, $managerRegistry, $repository);
    }

    public function getPreviewController(): string
    {
        return MailTemplateHeaderController::class . '::indexAction';
    }
}
