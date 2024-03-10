/**
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 */

((document, Joomla) => {
  'use strict';

  const copyToClipboardFallback = (input) => {
    input.focus();
    input.select();

    try {
      const copy = document.execCommand('copy');
      if (copy) {
        Joomla.renderMessages({message: [Joomla.Text._('COM_OAUTHSERVER_FIELD_COPY_SUCCESS')]});
      } else {
        Joomla.renderMessages({error: [Joomla.Text._('COM_OAUTHSERVER_FIELD_COPY_FAIL')]});
      }
    } catch (err) {
      Joomla.renderMessages({error: [err]});
    }
  };

  const copyToClipboard = () => {
    const buttons = document.querySelectorAll('[data-field-copy]');

    [].forEach.call(buttons, (button) => {
      button.addEventListener('click', ({currentTarget}) => {
        const selector = currentTarget.getAttribute('data-field-copy')
        const input = selector ? document.querySelector(selector) : currentTarget.previousElementSibling;

        if (!navigator.clipboard) {
          copyToClipboardFallback(input);
          return;
        }

        navigator.clipboard.writeText(input.value).then(() => {
          Joomla.renderMessages({message: [Joomla.Text._('COM_OAUTHSERVER_FIELD_COPY_SUCCESS')]});
        }, () => {
          Joomla.renderMessages({error: [Joomla.Text._('COM_OAUTHSERVER_FIELD_COPY_FAIL')]});
        });
      });
    })


  };

  const onBoot = () => {
    copyToClipboard();

    document.removeEventListener('DOMContentLoaded', onBoot);
  };

  document.addEventListener('DOMContentLoaded', onBoot);
})(document, Joomla);
