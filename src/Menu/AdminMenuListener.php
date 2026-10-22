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

        $configuration = $menu->getChild('configuration');
        if (null !== $configuration) {
            $configuration->addChild('roles', [
                'route' => 'arobases_sylius_rights_management_plugin_admin_role_index',
                'extras' => ['routes' => [
                    ['route' => 'arobases_sylius_rights_management_plugin_admin_role_create'],
                    ['route' => 'arobases_sylius_rights_management_plugin_admin_role_update'],
                ]],
            ])->setLabel('arobases_sylius_rights_management_plugin.menu.admin.roles')->setLabelAttribute('icon', 'tabler:users');
        }

        // The roles management is provided by this plugin: remove the Sylius Plus "Administration > Roles" placeholder
        $menu->removeChild('sylius.ui.administration');

        $adminUser = $this->currentAdminUserProvider->getCurrentAdminUser();
        if (null === $adminUser) {
            return;
        }

        foreach ($menu->getChildren() as $rootChildren) {
            // Root items without children (e.g. dashboard) are always displayed
            if (!$rootChildren->hasChildren()) {
                continue;
            }

            $hasCheckedChildren = false;
            $displayRootChildren = false;
            foreach ($rootChildren->getChildren() as $children) {
                // Items without routes (e.g. external links) are not managed by the rights
                if (!$children->getExtra('routes')) {
                    continue;
                }

                $hasCheckedChildren = true;
                $isGranted = true;
                foreach ($children->getExtra('routes') as $route) {
                    if (!$this->adminUserAccessChecker->isUserGranted($adminUser, $route['route'])) {
                        $isGranted = false;

                        break;
                    }
                }

                if ($isGranted) {
                    $displayRootChildren = true;
                } else {
                    $rootChildren->removeChild($children);
                }
            }

            if ($hasCheckedChildren && !$displayRootChildren) {
                $menu->removeChild($rootChildren);
            }
        }
    }
}
