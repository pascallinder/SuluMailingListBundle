<?php

namespace Linderp\SuluMailingListBundle\Admin\MailingList\Child;

use Linderp\SuluBaseBundle\Admin\AdminChild;
use Linderp\SuluBaseBundle\Admin\AdminCrudConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudFormConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudListConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudNavigationConfig;
use Linderp\SuluMailingListBundle\Entity\MailTemplateHeader\MailTemplateHeader;

class MailTemplateHeaderAdmin extends AbstractMailTemplateAdmin implements AdminChild
{
    public static function define(): AdminCrudConfig
    {
        return new AdminCrudConfig(
            MailTemplateHeader::RESOURCE_KEY,
            new AdminCrudNavigationConfig('mailTemplateHeader.nav.title'),
            new AdminCrudListConfig('mailTemplateHeader.list.title', 'mail_template_headers', 'app.mail_template_headers_list'),
            new AdminCrudFormConfig('subject', 'app.mail_template_header_add_form', 'app.mail_template_header_edit_form', 'mail_template_header_details'),
        );
    }
}
