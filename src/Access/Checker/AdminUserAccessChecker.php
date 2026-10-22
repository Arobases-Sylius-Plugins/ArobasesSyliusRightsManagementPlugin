<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Checker;

use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserInterface;
use Arobases\SyliusRightsManagementPlugin\Entity\Right;
use Arobases\SyliusRightsManagementPlugin\Entity\Role;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUserAccessChecker implements AdminUserAccessCheckerInterface
{
    private RouterInterface $router;

    private array $arrayListAllRoutes;

    /** @var \WeakMap<Role, array<string, true>> */
    private \WeakMap $authorizedRoutesByRole;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->arrayListAllRoutes = $this->router->getRouteCollection()->all();
        $this->authorizedRoutesByRole = new \WeakMap();
    }

    public function isUserGranted(UserInterface $adminUser, string $routeName): bool
    {
        if (!$adminUser instanceof AdminUserInterface) {
            return false;
        }

        $role = $adminUser->getRole();
        if (null === $role) {
            return false;
        }

        return isset($this->getRoleAuthorizedRoutes($role)[$routeName]);
    }

    /**
     * Resolved once per role and per request: the admin menu calls this checker for every menu item.
     *
     * @return array<string, true>
     */
    private function getRoleAuthorizedRoutes(Role $role): array
    {
        if (isset($this->authorizedRoutesByRole[$role])) {
            return $this->authorizedRoutesByRole[$role];
        }

        $authorizedRoutes = [];
        /** @var Right $right */
        foreach ($role->getRights() as $right) {
            $authorizedRoutes = array_merge($this->getRightAuthorizedRoutes($right), $authorizedRoutes);
        }

        return $this->authorizedRoutesByRole[$role] = array_fill_keys($authorizedRoutes, true);
    }

    private function getRightAuthorizedRoutes(Right $right): array
    {
        $authorizedRoutes = [];
        $excludedRoutes = [];
        foreach ($right->getRoutes() ?? [] as $route) {
            $authorizedRoutes = array_merge($this->resolveRoutes($route), $authorizedRoutes);
        }

        foreach ($right->getExcludedRoutes() ?? [] as $toExclude) {
            $excludedRoutes = array_merge($this->resolveRoutes($toExclude), $excludedRoutes);
        }

        foreach ($authorizedRoutes as $key => $exclude) {
            if (in_array($exclude, $excludedRoutes)) {
                unset($authorizedRoutes[$key]);
            }
        }

        return $authorizedRoutes;
    }

    private function resolveRoutes(string $routeName): array
    {
        if (strpos($routeName, '*')) {
            $routeName = substr($routeName, 0, -2);
        }
        $routeName = '/^' . $routeName . '/';
        $routes = $this->arrayListAllRoutes;
        $authorizedRoutes = [];

        foreach ($routes as $route => $value) {
            if (preg_match($routeName, $route)) {
                $authorizedRoutes[] = $route;
            }
        }

        return $authorizedRoutes;
    }
}
