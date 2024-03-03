<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * @var \Webmasterskaya\Component\OauthServer\Administrator\View\Client\HtmlView $this
 */

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>

<form action="<?php echo Route::_('index.php?option=com_oauthserver&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="client-form" aria-label="<?php echo Text::_('COM_OAUTHSERVER_CLIENT_FORM_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate">

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_OAUTHSERVER_CLIENT_DETAILS')); ?>

        <div class="row">
            <div class="col-12">
                <fieldset id="fieldset-publishingdata" class="options-form">
                    <legend><?php echo Text::_('COM_OAUTHSERVER_CLIENT'); ?></legend>
                    <div>
                        <?php echo $this->form->renderField('name'); ?>
                        <?php echo $this->form->renderField('public'); ?>
                        <?php echo $this->form->renderField('redirect_uri'); ?>
                        <?php echo $this->form->renderField('allow_plain_text_pkce'); ?>
                        <?php echo $this->form->renderField('identifier'); ?>
                        <?php echo $this->form->renderField('secret'); ?>
                    </div>
                </fieldset>
            </div>
        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

