<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

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

<form action="<?php echo Route::_('index.php?option=com_oauthserver&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="client-form"
      aria-label="<?php echo Text::_('COM_OAUTHSERVER_CLIENT_FORM_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>"
      class="form-validate">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_OAUTHSERVER_CLIENT_DETAILS')); ?>

        <div class="row">
            <div class="col-9">
                <fieldset id="fieldset-connections" class="options-form">
                    <legend><?php echo Text::_('COM_OAUTHSERVER_CLIENT_CONNECTIONS_TITLE'); ?></legend>
                    <div>
                        <?php echo $this->form->renderField('identifier'); ?>
                        <?php echo $this->form->renderField('secret'); ?>
                        <?php echo $this->form->renderField('redirect_uris'); ?>
                    </div>
                </fieldset>
                <fieldset id="fieldset-endpoints" class="options-form">
                    <legend><?php echo Text::_('COM_OAUTHSERVER_SERVER_CONNECTIONS_TITLE'); ?></legend>

                    <div>
                        <?php echo $this->form->renderField('authorize_url'); ?>
                        <?php echo $this->form->renderField('token_url'); ?>
                        <?php echo $this->form->renderField('profile_url'); ?>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset class="form-vertical">
                    <legend class="visually-hidden"><?php echo Text::_('JGLOBAL_FIELDSET_GLOBAL'); ?></legend>
                    <?php echo $this->form->renderField('active'); ?>
                    <?php echo $this->form->renderField('public'); ?>
                </fieldset>
            </div>
        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

