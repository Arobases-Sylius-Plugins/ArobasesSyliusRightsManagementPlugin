<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="arobases_sylius_rights_management_right")
 */
class Right implements ResourceInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /** @ORM\Column(type="string", length=100) */
    protected string $name;

    /**
     * @ORM\ManyToMany(targetEntity="Arobases\SyliusRightsManagementPlugin\Entity\Role",
     *     inversedBy="rights",
     *     fetch="EXTRA_LAZY",
     *      cascade={"persist", "remove"}
     * )
     *
     * * @ORM\JoinTable(name="arobases_sylius_rights_management_right_role",
     *      joinColumns={@ORM\JoinColumn(name="right_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    protected Collection $roles;

    /**
     * @ORM\ManyToOne(targetEntity="Arobases\SyliusRightsManagementPlugin\Entity\RightGroup",
     *     inversedBy="rights",
     *     fetch="EXTRA_LAZY",
     *      cascade={"persist", "remove"}
     * )
     */
    protected ?RightGroup $rightGroup = null;

    /** @ORM\Column(type="array", length=511, nullable=true) */
    protected ?array $routes = null;

    /** @ORM\Column(type="array", length=511, nullable=true) */
    protected ?array $excludedRoutes = null;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    public function getRightGroup(): ?RightGroup
    {
        return $this->rightGroup;
    }

    public function setRightGroup(?RightGroup $rightGroup): void
    {
        $this->rightGroup = $rightGroup;
    }

    public function getRoutes(): ?array
    {
        return $this->routes;
    }

    public function setRoutes(?array $routes): void
    {
        $this->routes = $routes;
    }

    public function getExcludedRoutes(): ?array
    {
        return $this->excludedRoutes;
    }

    public function setExcludedRoutes(?array $excludedRoutes): void
    {
        $this->excludedRoutes = $excludedRoutes;
    }
}
