<?php

namespace Linderp\SuluMailingListBundle\Admin\MailingList\Child;
use Linderp\SuluBaseBundle\Admin\AdminNavigationItem;
use Linderp\SuluMailingListBundle\Entity\NewsletterMail\NewsletterMail;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\View\DropdownToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class NewsletterMailAdmin extends Admin implements AdminNavigationItem
{
    public function __construct(
        private readonly KernelInterface $kernel,
        protected ViewBuilderFactoryInterface $viewBuilderFactory,
        protected readonly WebspaceManagerInterface $webspaceManager)
    {
    }

    private static function getListView():string
    {
        return 'newsletter_mails.list';
    }


    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();
        $listToolbarActions = [new ToolbarAction('sulu_admin.add'), new ToolbarAction('sulu_admin.delete',
            [
                'disabled_condition' => 'sent'
            ])];
        $addMailFormView = "app.newsletter_mail_add_form";
        $editMailFormView = "app.newsletter_mail_edit_form";
        $mailFormKey = "newsletter_mail_details";
        $mailList = $this->viewBuilderFactory->createListViewBuilder(self::getListView(), '/'.NewsletterMail::RESOURCE_KEY.'/:locale')
            ->setResourceKey(NewsletterMail::RESOURCE_KEY)
            ->setUserSettingsKey('newsletter_mails')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setListKey("newsletters_mails")
            ->setTabTitle('app.newsletter.tabs.newsletterMails')
            ->addToolbarActions($listToolbarActions)
            ->setAddView($addMailFormView)
            ->setEditView($editMailFormView);
        $viewCollection->add($mailList);

        $locales = $this->webspaceManager->getAllLocales();
        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder($addMailFormView,
            '/'.NewsletterMail::RESOURCE_KEY.'/:locale/add')
            ->setResourceKey(NewsletterMail::RESOURCE_KEY)
            ->setBackView(self::getListView())
            ->addLocales($locales);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder($addMailFormView . '.details', '/details')
            ->setResourceKey(NewsletterMail::RESOURCE_KEY)
            ->setFormKey($mailFormKey)
            ->setTabTitle('sulu_admin.details')
            ->setEditView($editMailFormView)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent($addMailFormView);
        $viewCollection->add($addDetailsFormView);

        // Configure Wardrobe Edit View
        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder($editMailFormView,
            '/'.NewsletterMail::RESOURCE_KEY.'/:locale/:id')
            ->setResourceKey(NewsletterMail::RESOURCE_KEY)
            ->setBackView(self::getListView())
            ->setTitleProperty("title")
            ->addLocales($locales);
        $viewCollection->add($editFormView);

        $editDetailsFormView = $this->viewBuilderFactory->createPreviewFormViewBuilder($editMailFormView . '.details',
            '/details')
            ->setResourceKey(NewsletterMail::RESOURCE_KEY)
            ->setFormKey($mailFormKey)
            ->setPreviewResourceKey('newsletters_mails')
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([
                new ToolbarAction('app.newsletter-subscription.send'),
                new ToolbarAction('sulu_admin.save'),
                new ToolbarAction('sulu_admin.delete',[
                    'visible_condition' => 'sent == false'
                ]),
                new DropdownToolbarAction(
                    'sulu_admin.edit',
                    'su-pen',
                    [
                        new ToolbarAction('sulu_admin.copy'),
                        new ToolbarAction('sulu_admin.copy_locale',[
                            'visible_condition' => 'sent == false'
                        ]),
                    ],
                )
            ]);
        if (isset($this->kernel->getBundles()['SuluAITranslatorBundle'])) {
            $editDetailsFormView->addToolbarActions([
                new ToolbarAction('ai_translator.toolbar',
                    ['allow_overwrite' => true,
                    'visible_condition' => 'sent == false'])
            ]);
        }
        $editDetailsFormView->setParent($editMailFormView);
        $viewCollection->add($editDetailsFormView);
    }
    public static function getNavigationItem(): NavigationItem
    {
        $navigationItem = new NavigationItem("mailingListMail.nav.title");
        $navigationItem->setView(static::getListView());
        return $navigationItem;
    }
}