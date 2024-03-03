<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\View\Client;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends \Joomla\CMS\MVC\View\HtmlView
{
    /**
     * Model state variables.
     *
     * @var  \Joomla\CMS\Object\CMSObject
     *
     * @since  1.0.0
     */
    protected CMSObject $state;

    /**
     * Form object.
     *
     * @var  \Joomla\CMS\Form\Form
     *
     * @since  1.0.0
     */
    protected Form $form;

    /**
     * The active item.
     *
     * @var  object
     *
     * @since  1.0.0
     */
    protected object $item;

    /**
     * Execute and display a template script.
     *
     * @param string $tpl The name of the template file to parse.
     *
     * @throws \Exception
     *
     * @since  1.0.0
     */
    public function display($tpl = null): void
    {
        $this->state = $this->get('State');
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode('\n', $errors), 500);
        }

        // Hard set layout
        $this->setLayout('edit');

        // Add title and toolbar
        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add title and toolbar.
     *
     * @throws \Exception
     * @since  1.0.0
     */
    protected function addToolbar(): void
    {
        // Hide main menu
        Factory::getApplication()->input->set('hidemainmenu', true);

        $canDo = ContentHelper::getActions('com_oauthserver', 'client');

        $user = $this->getCurrentUser();
        $toolbar = Toolbar::getInstance('toolbar');

        // Set page title
        $isNew = ($this->item->id == 0);
        $title = ($isNew) ? Text::_('COM_OAUTHSERVER_CLIENT_ADD') : Text::_('COM_OAUTHSERVER_CLIENT_EDIT');
        ToolbarHelper::title(Text::_('COM_OAUTHSERVER') . ': ' . $title, 'edit');

        if ($isNew && (count($user->getAuthorisedCategories('com_oauthserver', 'core.create')) > 0)) {
            $toolbar->apply('client.apply');

            $dropdown = $toolbar->dropdownButton('save-group');
            $dropdown->configure(
                function (Toolbar $actions) use ($user) {
                    $actions->save('client.save');
                    $actions->save2new('client.save2new');
                }
            );
        } else {
            $itemEditable = $canDo->get('core.edit');
            if ($itemEditable) {
                $toolbar->apply('client.apply');
            }

            $dropdown = $toolbar->dropdownButton('save-group');
            $dropdown->configure(
                function (Toolbar $childBar) use ($itemEditable, $canDo) {
                    if ($itemEditable) {
                        $childBar->save('client.save');
                        if ($canDo->get('core.create')) {
                            $childBar->save2new('client.save2new');
                        }
                        $childBar
                            ->standardButton('save-reset', 'COM_OAUTHSERVER_SAVE_AND_RESET')
                            ->task('client.save2reset')
                            ->icon('icon-sync')
                            ->formValidation(true);
                    }
                }
            );
        }

        $toolbar->cancel('client.cancel');

        ToolbarHelper::inlinehelp();
    }
}