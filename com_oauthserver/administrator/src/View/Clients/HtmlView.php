<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\View\Clients;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined('_JEXEC') or die;

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
     * An array of items.
     *
     * @var  array
     *
     * @since  1.0.0
     */
    protected array $items;

    /**
     * Pagination object.
     *
     * @var  \Joomla\CMS\Pagination\Pagination
     *
     * @since  1.0.0
     */
    protected Pagination $pagination;

    /**
     * Form object for search filters.
     *
     * @var  \Joomla\CMS\Form\Form;
     *
     * @since  1.0.0
     */
    public Form $filterForm;


    /**
     * The active search filters.
     *
     * @var  array
     *
     * @since  1.0.0
     */
    public array $activeFilters;

    /**
     * Is this view an Empty State.
     *
     * @var   bool
     *
     * @since  1.0.0
     */
    private bool $isEmptyState = false;

    /**
     * Display the view.
     *
     * @param   string  $tpl  The name of the template file to parse.
     *
     * @throws  \Exception
     *
     * @since  1.0.0
     */
    public function display($tpl = null): void
    {
        $this->state         = $this->get('State');
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Set empty state
        if (empty($this->items) && $this->isEmptyState = $this->get('IsEmptyState'))
        {
            $this->setLayout('emptystate');
        }

        // Check for errors
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode('\n', $errors), 500);
        }

        if ($this->getLayout() !== 'modal')
        {
            // Add title and toolbar
            $this->addToolbar();
        }

        // Set status field value
        $this->filterForm->setValue('status', null, $this->state->get('filter.status'));

        parent::display($tpl);
    }

    /**
     * Add title and toolbar.
     *
     * @since  1.0.0
     */
    protected function addToolbar(): void
    {
        $canDo = ContentHelper::getActions('com_oauthserver', 'clients');

        $user    = $this->getCurrentUser();
        $toolbar = Toolbar::getInstance('toolbar');

        // Set page title
        ToolbarHelper::title(Text::_('COM_OAUTHSERVER') . ': ' . Text::_('COM_OAUTHSERVER_CLIENTS'));

        // Add create button
        if ($canDo->get('core.create')
            || \count($user->getAuthorisedCategories('com_oauthserver', 'core.create')) > 0)
        {
            $toolbar->addNew('client.add');
        }

        if ($canDo->get('core.delete'))
        {
            $toolbar->delete('clients.delete')
                ->text('JTOOLBAR_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        // Add preferences button
        if ($user->authorise('core.admin', 'com_oauthserver')
            || $user->authorise('core.options', 'com_oauthserver'))
        {
            $toolbar->preferences('com_oauthserver');
        }
    }
}
