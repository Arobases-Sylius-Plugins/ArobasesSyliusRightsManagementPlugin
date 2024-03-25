<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AdminUserTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Arobases\SyliusRightsManagementPlugin\Entity\Role",
     *     inversedBy="adminUsers",
     *     fetch="EXTRA_LAZY",
     *      cascade={"persist"}
     * )
     */
    protected ?Role $role = null;

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): void
    {
        $this->role = $role;
    }
}
