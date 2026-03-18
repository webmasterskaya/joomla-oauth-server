<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\AuthorizationValidators;

use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class JwtInQueryTokenValidator implements AuthorizationValidatorInterface
{
    use CryptTrait;

    /**
     * @var AccessTokenRepositoryInterface
     */
    private $accessTokenRepository;

    /**
     * @var CryptKey
     */
    protected $publicKey;

    /**
     * @var Configuration
     */
    private $jwtConfiguration;

    /**
     * @var \DateInterval|null
     */
    private $jwtValidAtDateLeeway;

    /**
     * @var string
     */
    private $queryParamName;

    /**
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     * @param \DateInterval|null $jwtValidAtDateLeeway
     * @param string $queryParamName
     */
    public function __construct(AccessTokenRepositoryInterface $accessTokenRepository, \DateInterval $jwtValidAtDateLeeway = null, string $queryParamName = 'access_token')
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->jwtValidAtDateLeeway = $jwtValidAtDateLeeway;
        $this->queryParamName = $queryParamName;
    }

    /**
     * Set the public key
     *
     * @param CryptKey $key
     */
    public function setPublicKey(CryptKey $key)
    {
        $this->publicKey = $key;

        $this->initJwtConfiguration();
    }

    /**
     * Initialise the JWT configuration.
     */
    private function initJwtConfiguration()
    {
        $this->jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('empty', 'empty')
        );

        $clock = new SystemClock(new \DateTimeZone(\date_default_timezone_get()));
        $this->jwtConfiguration->setValidationConstraints(
            new LooseValidAt($clock, $this->jwtValidAtDateLeeway),
            new SignedWith(
                new Sha256(),
                InMemory::plainText($this->publicKey->getKeyContents(), $this->publicKey->getPassPhrase() ?? '')
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthorization(ServerRequestInterface $request)
    {
        $query = $request->getQueryParams();
        if (!key_exists($this->queryParamName, $query)
            || empty($query[$this->queryParamName])) {
            throw OAuthServerException::accessDenied(sprintf('Missing parameter "%s" in query', $this->queryParamName));
        }

        $jwt = \trim((string)$query[$this->queryParamName]);

        try {
            // Attempt to parse the JWT
            $token = $this->jwtConfiguration->parser()->parse($jwt);
        } catch (\Lcobucci\JWT\Exception $exception) {
            throw OAuthServerException::accessDenied($exception->getMessage(), null, $exception);
        }

        try {
            // Attempt to validate the JWT
            $constraints = $this->jwtConfiguration->validationConstraints();
            $this->jwtConfiguration->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $exception) {
            throw OAuthServerException::accessDenied('Access token could not be verified', null, $exception);
        }

        $claims = $token->claims();

        // Check if token has been revoked
        if ($this->accessTokenRepository->isAccessTokenRevoked($claims->get('jti'))) {
            throw OAuthServerException::accessDenied('Access token has been revoked');
        }

        // Return the request with additional attributes
        return $request
            ->withAttribute('oauth_access_token_id', $claims->get('jti'))
            ->withAttribute('oauth_client_id', $this->convertSingleRecordAudToString($claims->get('aud')))
            ->withAttribute('oauth_user_id', $claims->get('sub'))
            ->withAttribute('oauth_scopes', $claims->get('scopes'));
    }

    /**
     * Convert single record arrays into strings to ensure backwards compatibility between v4 and v3.x of lcobucci/jwt
     *
     * @param mixed $aud
     *
     * @return array|string
     */
    private function convertSingleRecordAudToString($aud)
    {
        return \is_array($aud) && \count($aud) === 1 ? $aud[0] : $aud;
    }
}
