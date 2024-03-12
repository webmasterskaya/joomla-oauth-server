<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Model\AuthCodeModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\ClientModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\AuthCode;

\defined('_JEXEC') or die;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    private AuthCodeModel $authCodeModel;

    private ClientModel $clientModel;

    /**
     * @param   AuthCodeModel  $authCodeModel
     * @param   ClientModel    $clientModel
     *
     * @since version
     */
    public function __construct(AuthCodeModel $authCodeModel, ClientModel $clientModel)
    {
        $this->authCodeModel = $authCodeModel;
        $this->clientModel   = $clientModel;
    }

    public function getNewAuthCode(): AuthCode
    {
        return new AuthCode();
    }

    /**
     * @param   AuthCodeEntityInterface  $authCodeEntity
     *
     * @return void
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws \Exception
     * @since version
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $authCode = $this->authCodeModel->getItemByIdentifier($authCodeEntity->getIdentifier());

        if ($authCode !== false)
        {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $data = $authCodeEntity->getData();

        $client = $this->clientModel->getItemByIdentifier($data['client_identifier']);

        if ($client === false)
        {
            throw new \RuntimeException($this->clientModel->getError());
        }

        $data['client_id'] = $client->id;
        unset($data['client_identifier']);

        $this->authCodeModel->save($data);
    }

    public function revokeAuthCode($codeId): void
    {
        $this->authCodeModel->revoke($codeId);
    }

    /**
     * @param   string  $codeId
     *
     * @throws \Exception
     * @since version
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        $authCode = $this->authCodeModel->getItemByIdentifier($codeId);

        if ($authCode === false)
        {
            return true;
        }

        return (bool) $authCode->revoked;
    }
}
