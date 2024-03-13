<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Event;

abstract class AbstractImmutableEvent extends \Joomla\CMS\Event\AbstractImmutableEvent
{
    /**
     * A flag to see if the constructor has been already called.
     *
     * @var    boolean
     * @since  version
     */
    private bool $constructed = false;

    /**
     * Constructor.
     *
     * @param   string  $name       The event name.
     * @param   array   $arguments  The event arguments.
     *
     * @throws  \BadMethodCallException
     * @since        version
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(string $name, array $arguments = [])
    {
        if ($this->constructed)
        {
            throw new \BadMethodCallException(
                sprintf('Cannot reconstruct the AbstractImmutableEvent %s.', $this->name)
            );
        }

        $this->name      = $name;
        $this->arguments = [];

        foreach ($arguments as $argumentName => $value)
        {
            $this->setArgument($argumentName, $value);
        }

        $this->constructed = true;
    }

    public function setArgument($name, $value): AbstractImmutableEvent
    {
        if (!$this->constructed)
        {
            return parent::setArgument($name, $value);
        }

        throw new \BadMethodCallException(
            sprintf(
                'Cannot set the argument %s of the immutable event %s.',
                $name,
                $this->name
            )
        );
    }

    public function addArgument($name, $value): AbstractImmutableEvent
    {
        if (!$this->constructed)
        {
            return parent::addArgument($name, $value);
        }

        throw new \BadMethodCallException(
            sprintf(
                'Cannot add the argument %s of the immutable event %s.',
                $name,
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
