<?php

declare(strict_types=1);

namespace Tests\Arobases\SyliusRightsManagementPlugin\Entity;

use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserInterface;
use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\AdminUser as BaseAdminUser;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_admin_user')]
class AdminUser extends BaseAdminUser implements AdminUserInterface
{
    use AdminUserTrait;
}
