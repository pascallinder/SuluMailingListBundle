<?php

namespace Linderp\SuluMailingListBundle\Admin\MailingList\Child;

use Linderp\SuluBaseBundle\Admin\AdminChild;
use Linderp\SuluBaseBundle\Admin\AdminCrudConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudFormConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudListConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudNavigationConfig;
use Linderp\SuluMailingListBundle\Entity\MailTemplateFooter\MailTemplateFooter;

class MailTemplateFooterAdmin extends AbstractMailTemplateAdmin implements AdminChild
{
    public static function define(): AdminCrudConfig
    {
        return new AdminCrudConfig(
            MailTemplateFooter::RESOURCE_KEY,
            new AdminCrudNavigationConfig('mailTemplateFooter.nav.title'),
            new AdminCrudListConfig('mailTemplateFooter.list.title', 'mail_template_footers', 'app.mail_template_footers_list'),
            new AdminCrudFormConfig('subject', 'app.mail_template_footer_add_form', 'app.mail_template_footer_edit_form', 'mail_template_footer_details'),
        );
    }
}
