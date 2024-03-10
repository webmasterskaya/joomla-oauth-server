<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var  string $id    DOM id of the field.
 * @var  string $label Label of the field.
 * @var  string $name  Name of the input field.
 * @var  string $value Value attribute of the field.
 */

Text::script('ERROR');
Text::script('MESSAGE');
Text::script('COM_OAUTHSERVER_FIELD_COPY_SUCCESS');
Text::script('COM_OAUTHSERVER_FIELD_COPY_FAIL');

Factory::getApplication()->getDocument()->getWebAssetManager()
    ->useScript('com_oauthserver.field.copy');

?>
<div class="input-group">
    <input
        type="text"
        class="form-control"
        name="<?php echo $name; ?>"
        id="<?php echo $id; ?>"
        readonly
        value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>">
    <button
        class="btn btn-primary"
        type="button"
        data-field-copy="#<?php echo $id; ?>"
        title="<?php echo Text::_('COM_OAUTHSERVER_FIELD_COPY_DESC'); ?>"><?php echo Text::_('COM_OAUTHSERVER_FIELD_COPY'); ?></button>
</div>
