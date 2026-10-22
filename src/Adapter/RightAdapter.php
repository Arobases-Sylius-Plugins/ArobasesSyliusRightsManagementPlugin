<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Adapter;

class RightAdapter
{
    private array $rights;

    public function __construct(array $rights)
    {
        $this->rights = $rights;
    }

    public function getRightsFromYaml(): ?array
    {
        return $this->rights;
    }
}
