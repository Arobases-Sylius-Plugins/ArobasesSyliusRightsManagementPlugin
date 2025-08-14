<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Menu;

use Arobases\SyliusRightsManagementPlugin\Access\Checker\AdminUserAccessCheckerInterface;
use Arobases\SyliusRightsManagementPlugin\Provider\CurrentAdminUserProviderInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener implements AdminMenuListenerInterface
{
    private AdminUserAccessCheckerInterface $adminUserAccessChecker;

    private CurrentAdminUserProviderInterface $currentAdminUserProvider;

    public function __construct(AdminUserAccessCheckerInterface $adminUserAccessChecker, CurrentAdminUserProviderInterface $currentAdminUserProvider)
    {
        $this->adminUserAccessChecker = $adminUserAccessChecker;
        $this->currentAdminUserProvider = $currentAdminUserProvider;
    }

    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $menu->getChild('configuration')->addChild('roles', [
            'route' => 'arobases_sylius_rights_management_plugin_admin_role_index',
        ])->setLabel('arobases_sylius_rights_management_plugin.menu.admin.roles')->setLabelAttribute('icon', 'users');

        foreach ($menu->getChildren() as $rootChildren) {
            $displayRootChildren = false;
            foreach ($rootChildren->getChildren() as $children) {
                if (!$children->getExtra('routes')) {
                    continue;
                }
                foreach ($children->getExtra('routes') as $route) {
                    if (!$this->adminUserAccessChecker->isUserGranted($this->currentAdminUserProvider->getCurrentAdminUser(), $route['route'])) {
                        $rootChildren->removeChild($children);
                    } else {
                        $displayRootChildren = true;
                    }
                }
            }
            if (!$displayRootChildren) {
                $menu->removeChild($rootChildren);
            }
        }
    }
}
