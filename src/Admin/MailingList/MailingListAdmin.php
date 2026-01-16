<?php

namespace Linderp\SuluMailingListBundle\Admin\MailingList;
use Linderp\SuluMailingListBundle\Admin\MailingList\Child\NewsletterEntryAdmin;
use Linderp\SuluMailingListBundle\Admin\MailingList\Child\NewsletterMailAdmin;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;

class MailingListAdmin extends Admin
{
    public static string $NAME='mailingList.nav.title';
    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $parentModule = new NavigationItem(self::$NAME);
        $parentModule->setPosition(30);
        $parentModule->setIcon('fa-newspaper');

        $parentModule->addChild(NewsletterEntryAdmin::getNavigationItem());
        $parentModule->addChild(NewsletterMailAdmin::getNavigationItem());

        $navigationItemCollection->add($parentModule);
    }
}