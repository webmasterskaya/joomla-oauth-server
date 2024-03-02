<?php

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * @var \Webmasterskaya\Component\OauthServer\Administrator\View\Clients\HtmlView $this
 */

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');
$user = Factory::getApplication()->getIdentity();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_oauthserver&view=clients'); ?>" method="post" name="adminForm"
      id="adminForm">

    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span
                                class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table" id="clientList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_OAUTHSERVER_CLIENTS_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_OAUTHSERVER_CLIENTS_HEADING_NAME', 'client.name', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col">
                                <?php echo Text::_('COM_OAUTHSERVER_CLIENTS_HEADING_IDENTIFIER'); ?>
                            </th>
                            <th scope="col">
                                <?php echo Text::_('COM_OAUTHSERVER_CLIENTS_HEADING_SECRET'); ?>
                            </th>
                            <th scope="col">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_OAUTHSERVER_CLIENTS_HEADING_PUBLIC', 'client.public', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'client.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($this->items as $i => $item) : ?>

                            <?php $canCreate = $user->authorise('core.create', 'com_oauthserver.clients');
                            $canEdit = $user->authorise('core.edit', 'com_oauthserver.clients');
                            $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || is_null($item->checked_out);
                            $canChange = $user->authorise('core.edit.state', 'com_oauthserver.clients') && $canCheckin; ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
                                </td>
                                <td class="small text-start">
                                    <?php echo $item->name; ?>
                                </td>
                                <td class="small text-center d-none d-md-table-cell">
                                    <?php echo $item->identifier; ?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $item->secret; ?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo Text::_((bool) $item->public ? 'JYES' : 'JNO'); ?>
                                </td>
                                <td class="small text-center">
                                    <?php echo $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>