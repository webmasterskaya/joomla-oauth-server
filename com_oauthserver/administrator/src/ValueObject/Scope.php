<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\ValueObject;

final class Scope
{
    private string $identifier;

    private ?string $name;

    private ?string $description;

    private ?string $parent;

    /**
     * @param   string       $identifier
     * @param   string|null  $name
     * @param   string|null  $description
     * @param   string|null  $parent
     *
     * @since version
     */
    public function __construct(string $identifier, ?string $name = null, ?string $description = null, ?string $parent = null)
    {
        $this->identifier  = $this->cleanUp($identifier);
        $this->name        = $name;
        $this->description = $description;
        $this->parent      = $this->cleanUp($parent);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $this->cleanUp($identifier);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(string $parent): void
    {
        $this->parent = $this->cleanUp($parent);
    }

    private function cleanUp(?string $string): ?string
    {
        return is_null($string) ? null : preg_replace('/[^A-Z0-9_-]/i', '', $string);
    }
}
