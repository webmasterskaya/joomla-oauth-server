<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Repository;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Webmasterskaya\Component\OauthServer\Administrator\Model\AccessTokenModel;
use Webmasterskaya\Component\OauthServer\Administrator\Model\RefreshTokenModel;
use Webmasterskaya\Component\OauthServer\Site\Entity\RefreshToken;

\defined('_JEXEC') or die;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

    private RefreshTokenModel $refreshTokenModel;

    private AccessTokenModel $accessTokenModel;

    /**
     * @param   RefreshTokenModel  $refreshTokenModel
     * @param   AccessTokenModel   $accessTokenModel
     *
     * @since version
     */
    public function __construct(RefreshTokenModel $refreshTokenModel, AccessTokenModel $accessTokenModel)
    {
        $this->refreshTokenModel = $refreshTokenModel;
        $this->accessTokenModel  = $accessTokenModel;
    }


    public function getNewRefreshToken(): RefreshToken
    {
        return new RefreshToken();
    }

    /**
     * @param   RefreshTokenEntityInterface  $refreshTokenEntity
     *
     * @return void
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws \Exception
     * @since version
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $refreshToken = $this->refreshTokenModel->getItemByIdentifier($refreshTokenEntity->getIdentifier());

        if ($refreshToken !== false)
        {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $data = $refreshTokenEntity->getData();

        $accessToken = $this->accessTokenModel->getItemByIdentifier($data['access_token_identifier']);

        if ($accessToken === false)
        {
            throw new \RuntimeException($this->accessTokenModel->getError());
        }

        unset($data['access_token_identifier']);
        $data['access_token_id'] = $accessToken->id;

        $this->refreshTokenModel->save($data);
    }

    public function revokeRefreshToken($tokenId): void
    {
        $this->refreshTokenModel->revoke($tokenId);
    }

    /**
     * @param   string  $tokenId
     *
     * @throws \Exception
     * @since version
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        $refreshToken = $this->refreshTokenModel->getItemByIdentifier($tokenId);

        if ($refreshToken === false)
        {
            return true;
        }

        return (bool) $refreshToken->revoked;
    }
}
