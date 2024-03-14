<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Event;

use Joomla\CMS\Event\AbstractEvent;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Webmasterskaya\Component\OauthServer\Site\Entity\Scope;

class ScopeResolveEvent extends AbstractEvent
{
    private bool $constructed = false;

    public function __construct(string $name, array $arguments = [])
    {
        if (!array_key_exists('scopes', $arguments))
        {
            throw new \BadMethodCallException("Argument 'scopes' is required for event $name");
        }

        if (!array_key_exists('grant', $arguments))
        {
            throw new \BadMethodCallException("Argument 'grant' is required for event $name");
        }

        if (!array_key_exists('client', $arguments))
        {
            throw new \BadMethodCallException("Argument 'client' is required for event $name");
        }

        parent::__construct('onScopeResolve', $arguments);

        $this->constructed = true;
    }

    protected function onSetScopes(array $scopes): array
    {
        foreach ($scopes as &$scope)
        {
            if (!($scope instanceof Scope))
            {
                throw new \InvalidArgumentException(sprintf("Argument 'scopes' must be array of '%s' in class '%s'. '%s' given.",
                    Scope::class,
                    get_class($this),
                    get_debug_type($scope)
                ));
            }
        }

        return $scopes;
    }

    protected function onSetGrant(string $grant): string
    {
        if (!$this->constructed)
        {
            return $grant;
        }

        throw new \BadMethodCallException(
            sprintf(
                "Cannot set the argument 'grant' of the event %s after initialize.",
                $this->name
            )
        );
    }

    protected function onSetClient(ClientEntityInterface $client): ClientEntityInterface
    {
        if (!$this->constructed)
        {
            return $client;
        }

        throw new \BadMethodCallException(
            sprintf(
                "Cannot set the argument 'client' of the event %s after initialize.",
                $this->name
            )
        );
    }

    protected function onSetUserId(?int $userId): ?int
    {
        if (!$this->constructed)
        {
            return $userId;
        }

        throw new \BadMethodCallException(
            sprintf(
                "Cannot set the argument 'userId' of the event %s after initialize.",
                $this->name
            )
        );
    }

    public function removeArgument($name)
    {
        if (!$this->constructed)
        {
            return parent::removeArgument($name);
        }

        throw new \BadMethodCallException(
            sprintf(
                'Cannot remove the argument %s of the immutable event %s.',
                $name,
                $this->name
            )
        );
    }

    public function clearArguments(): array
    {
        if (!$this->constructed)
        {
            return parent::clearArguments();
        }

        throw new \BadMethodCallException(
            sprintf(
                'Cannot clear arguments of the immutable event %s.',
                $this->name
            )
        );
    }
}
