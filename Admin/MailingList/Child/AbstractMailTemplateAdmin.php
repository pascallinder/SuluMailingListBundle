<?php

namespace Linderp\SuluMailingListBundle\Admin\MailingList\Child;

use Linderp\SuluBaseBundle\Admin\AdminCrud;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ReferenceBundle\Infrastructure\Sulu\Admin\View\ReferenceViewBuilderFactoryInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractMailTemplateAdmin extends AdminCrud
{
    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        ActivityViewBuilderFactoryInterface $activityViewBuilderFactory,
        ReferenceViewBuilderFactoryInterface $referenceViewBuilderFactory,
        WebspaceManagerInterface $webspaceManager,
        private readonly KernelInterface $kernel,
    ) {
        parent::__construct(
            $viewBuilderFactory,
            $activityViewBuilderFactory,
            $referenceViewBuilderFactory,
            $webspaceManager,
        );
    }

    protected function buildEditToolbarActions(): array
    {
        $toolbarActions = [
            ...parent::buildEditToolbarActions(),
            new ToolbarAction('sulu_admin.copy_locale'),
        ];

        if (isset($this->kernel->getBundles()['SuluAITranslatorBundle'])) {
            $toolbarActions[] = new ToolbarAction('ai_translator.toolbar', [
                'allow_overwrite' => true,
            ]);
        }

        return $toolbarActions;
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();
        $definition = $this->getDefinition();
        $route = '/' . $definition->resourceKey . '/:locale';

        $listView = $this->viewBuilderFactory->createListViewBuilder($definition->list->view, $route)
            ->setResourceKey($definition->resourceKey)
            ->setListKey($definition->list->key)
            ->setTitle($definition->list->title)
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddView($definition->form->addView)
            ->setEditView($definition->form->editView)
            ->addToolbarActions($this->buildListToolbarActions());
        $viewCollection->add($listView);

        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder($definition->form->addView, $route . '/add')
            ->setResourceKey($definition->resourceKey)
            ->setBackView($definition->list->view)
            ->addLocales($locales);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder($definition->form->addView . '.details', '/details')
            ->setResourceKey($definition->resourceKey)
            ->setFormKey($definition->form->key)
            ->setTabTitle('sulu_admin.details')
            ->setEditView($definition->form->editView)
            ->addToolbarActions($this->buildAddToolbarActions())
            ->setParent($definition->form->addView);
        $viewCollection->add($addDetailsFormView);

        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder($definition->form->editView, $route . '/:id')
            ->setResourceKey($definition->resourceKey)
            ->setBackView($definition->list->view)
            ->setTitleProperty($definition->form->titleProperty)
            ->addLocales($locales);
        $viewCollection->add($editFormView);

        $editDetailsFormView = $this->viewBuilderFactory->createPreviewFormViewBuilder($definition->form->editView . '.details', '/details')
            ->setResourceKey($definition->resourceKey)
            ->setFormKey($definition->form->key)
            ->setPreviewResourceKey($definition->resourceKey)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($this->buildEditToolbarActions())
            ->setParent($definition->form->editView);
        $viewCollection->add($editDetailsFormView);
    }
}
