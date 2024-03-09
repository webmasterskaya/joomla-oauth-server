<?php
/**
 * @package         Joomla.Site
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Site\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

\defined('_JEXEC') or die;

final class Scope implements ScopeEntityInterface
{
    use EntityTrait;

    protected ?string $description;


    /**
     * @return mixed
     * @since version
     */
    public function jsonSerialize(): mixed
    {
        return $this->getIdentifier();
    }

    /**
     * @return string|null
     * @since version
     */
    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    /**
     * @param   string  $description
     *
     * @since version
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }


}
