<?php

namespace Linderp\SuluMailingListBundle\Admin\MailingList\Child;
use Linderp\SuluBaseBundle\Admin\AdminChild;
use Linderp\SuluBaseBundle\Admin\AdminCrud;
use Linderp\SuluBaseBundle\Admin\AdminCrudConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudFormConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudListConfig;
use Linderp\SuluBaseBundle\Admin\AdminCrudNavigationConfig;
use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Linderp\SuluMailingListBundle\Entity\NewsletterSubscription\NewsletterSubscription;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ReferenceBundle\Infrastructure\Sulu\Admin\View\ReferenceViewBuilderFactoryInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class NewsletterEntryAdmin extends AdminCrud implements AdminChild
{
    public function __construct(
        private readonly KernelInterface $kernel,
        protected ViewBuilderFactoryInterface $viewBuilderFactory,
                                protected ActivityViewBuilderFactoryInterface $activityViewBuilderFactory,
                                protected ReferenceViewBuilderFactoryInterface $referenceViewBuilderFactory,
                                protected WebspaceManagerInterface $webspaceManager)
    {
        parent::__construct(
            $this->viewBuilderFactory,
            $this->activityViewBuilderFactory,
            $this->referenceViewBuilderFactory,
            $this->webspaceManager
        );
    }
    public static function define(): AdminCrudConfig
    {
        return new AdminCrudConfig(
            Newsletter::RESOURCE_KEY,
            new AdminCrudNavigationConfig(
                "mailingList.nav.title"
            ),
            new AdminCrudListConfig(
                "mailingList.list.title",
                "newsletters",
                "app.newsletters_list"
            ),
            new AdminCrudFormConfig(
                "title",
                "app.newsletter_add_form",
                "app.newsletter_edit_form",
                "newsletter_details"
            )
        );
    }
    public function configureViews(ViewCollection $viewCollection): void
    {
        parent::configureViews($viewCollection);
        $doubleOptMailForm = $this->viewBuilderFactory->createPreviewFormViewBuilder($this->getDefinition()->form->editView.'double-opt-mail','/double-opt-mail/')
            ->setResourceKey(Newsletter::RESOURCE_KEY)
            ->setFormKey('newsletter_double_opt_details')
            ->setTabTitle('mailingList.tabs.doubleOptMail')
            ->addToolbarActions([
                new ToolbarAction('sulu_admin.save'),
                new ToolbarAction('sulu_admin.copy_locale'),
            ]);
        if (isset($this->kernel->getBundles()['SuluAITranslatorBundle'])) {
            $doubleOptMailForm->addToolbarActions([
                new ToolbarAction('ai_translator.toolbar',
                    ['allow_overwrite' => true])
            ]);
        }
        $doubleOptMailForm->setParent($this->getDefinition()->form->editView);
        $viewCollection->add($doubleOptMailForm);

        $locales = $this->webspaceManager->getAllLocales();
        $addSubscriptionFormView = "app.newsletter_subscription_add_form";
        $subscriptionFormKey = "newsletter_subscription_details";
        $subscriptionListToolbarActions = [new ToolbarAction(
            'app.newsletter-subscription.subscribe'
        ),new ToolbarAction(
            'app.newsletter-subscription.unsubscribe'
        )];
        $subscriptionList = $this->viewBuilderFactory->createListViewBuilder($this->getDefinition()->list->view . '.subscriptions', '/subscriptions')
            ->setResourceKey(NewsletterSubscription::RESOURCE_KEY)
            ->addListAdapters(['table'])
            ->setListKey("newsletters_subscriptions")
            ->addRouterAttributesToListRequest(['id'=>'newsletter_id'])
            ->setUserSettingsKey('newsletter_subscriptions')
            ->setTabTitle('mailingList.tabs.subscriptions')
            ->addToolbarActions($subscriptionListToolbarActions)
            ->setAddView($addSubscriptionFormView)
            ->disableColumnOptions()
            ->addRouterAttributesToListMetadata(['id'=>'newsletter_id'])
            ->setParent($this->getDefinition()->form->editView);
        $viewCollection->add($subscriptionList);


        $addFormViewSubscription = $this->viewBuilderFactory->createResourceTabViewBuilder($addSubscriptionFormView,
            '/subscriptions/add')
            ->setResourceKey(Newsletter::RESOURCE_KEY)
            ->setBackView($this->getDefinition()->list->view. '.subscriptions')
            ->setTabTitle('mailingList.tabs.addSubscriptions')
            ->setParent($this->getDefinition()->form->editView);
        $viewCollection->add($addFormViewSubscription);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder($addSubscriptionFormView . '.details',
            '/details')
            ->setResourceKey(NewsletterSubscription::RESOURCE_KEY)
            ->setFormKey($subscriptionFormKey)
            ->addRouterAttributesToFormRequest(['id'=>'newsletter_id'])
            ->addRouterAttributesToFormMetadata(['id'=>'newsletter_id'])
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent($addSubscriptionFormView);
        $viewCollection->add($addDetailsFormView);
    }

  
}