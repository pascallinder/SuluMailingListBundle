<?php

namespace Linderp\SuluMailingListBundle\Entity;

use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;

interface MailTemplateAwareInterface
{
    public function getHeader(): ?MailTemplateHeader;

    public function setHeader(?MailTemplateHeader $header): void;

    public function getFooter(): ?MailTemplateFooter;

    public function setFooter(?MailTemplateFooter $footer): void;
}
