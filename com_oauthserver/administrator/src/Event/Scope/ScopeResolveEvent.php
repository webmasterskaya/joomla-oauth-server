<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Event\Scope;

use Joomla\CMS\Event\AbstractEvent;
use Webmasterskaya\Component\OauthServer\Site\Entity\Scope;

class ScopeResolveEvent extends AbstractEvent
{
    private bool $is_constructed;

    public function __construct(array $scopes, string $grant, object $client, ?int $user_id)
    {
        $arguments = [
            'scopes' => $scopes,
            'grant' => $grant,
            'client' => $client,
            'user_id' => $user_id
        ];

        $this->is_constructed = false;

        parent::__construct('onOauthServerScopeResolve', $arguments);

        $this->is_constructed = true;
    }

    protected function onSetScopes(array $scopes): array
    {
        foreach ($scopes as &$scope) {
            if (!($scope instanceof Scope)) {
                throw new \InvalidArgumentException(sprintf('Argument "scopes" must be array of "%s" in class "%s". "%s" given.',
                    Scope::class,
                    get_class($this),
                    get_debug_type($scope)
                ));
            }
        }

        return $scopes;
    }

    protected function onSetGrant(string $grant)
    {
        if ($this->is_constructed) {
            $grant = $this->getArgument('grant');
        }
        return $grant;
    }

    protected function onSetClient(object $client)
    {
        if ($this->is_constructed) {
            $client = $this->getArgument('client');
        }
        return $client;
    }

    protected function onSetUser_id(?int $user_id)
    {
        if ($this->is_constructed) {
            $user_id = $this->getArgument('user_id');
        }
        return $user_id;
    }

    public function removeArgument($name)
    {
        throw new \BadMethodCallException(
            sprintf(
                'Cannot remove the argument %s of the event %s.',
                $name,
                $this->name
            )
        );
    }

    public function clearArguments()
    {
        throw new \BadMethodCallException(
            sprintf(
                'Cannot clear arguments of the event %s.',
                $this->name
            )
        );
    }
}