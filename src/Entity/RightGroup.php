<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="arobases_sylius_rights_management_right_group")
 */
class RightGroup implements ResourceInterface
{
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
     * @ORM\OneToMany(targetEntity="Arobases\SyliusRightsManagementPlugin\Entity\Right",
     *     mappedBy="rightGroup", fetch="EXTRA_LAZY",
     *     cascade={"persist", "remove"}
     *      )
     */
    protected Collection $rights;

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

    public function getRights(): Collection
    {
        return $this->rights;
    }

    public function setRights(Collection $rights): void
    {
        $this->rights = $rights;
    }
}
