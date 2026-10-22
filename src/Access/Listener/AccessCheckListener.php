<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Listener;

use Arobases\SyliusRightsManagementPlugin\Access\Checker\AdminRouteCheckerInterface;
use Arobases\SyliusRightsManagementPlugin\Access\Checker\AdminUserAccessCheckerInterface;
use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserInterface as RoleAwareAdminUserInterface;
use Arobases\SyliusRightsManagementPlugin\Provider\CurrentAdminUserProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;

class AccessCheckListener implements AccessCheckListenerInterface
{
    /**
     * Routes always reachable by an administrator, whatever their rights
     * (dashboard, security, live components and autocomplete used by the admin forms).
     */
    private const ALWAYS_GRANTED_ROUTES = [
        'sylius_admin_dashboard',
        'sylius_admin_login',
        'sylius_admin_login_check',
        'sylius_admin_logout',
        'sylius_admin_render_reset_password_page',
        'sylius_admin_request_password_reset',
        'sylius_admin_render_password_reset',
        'sylius_admin_password_reset',
        'sylius_admin_live_component',
        'sylius_admin_entity_autocomplete',
    ];

    private CurrentAdminUserProviderInterface $currentAdminUserProvider;

    private AdminUserAccessCheckerInterface $adminUserAccessChecker;

    private AdminRouteCheckerInterface $adminRouteAccessChecker;

    private RequestStack $requestStack;

    private RouterInterface $router;

    public function __construct(CurrentAdminUserProviderInterface $currentAdminUserProvider, AdminUserAccessCheckerInterface $adminUserAccessChecker, AdminRouteCheckerInterface $adminRouteAccessChecker, RequestStack $requestStack, RouterInterface $router)
    {
        $this->currentAdminUserProvider = $currentAdminUserProvider;
        $this->adminUserAccessChecker = $adminUserAccessChecker;
        $this->adminRouteAccessChecker = $adminRouteAccessChecker;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        $routeName = $event->getRequest()->attributes->get('_route');

        if (!is_string($routeName)) {
            return;
        }

        if (strpos($routeName, 'partial') || in_array($routeName, self::ALWAYS_GRANTED_ROUTES, true)) {
            return;
        }

        if (!$this->adminRouteAccessChecker->isAdminRoute($routeName)) {
            return;
        }

        $adminUser = $this->currentAdminUserProvider->getCurrentAdminUser();

        if (!$adminUser instanceof RoleAwareAdminUserInterface) {
            return;
        }

        if ($adminUser->getRole() === null) {
            $event->setResponse($this->redirectUser($this->getRedirectRoute(), $this->getRedirectMessage()));
        }

        if ($adminUser instanceof AdminUserInterface && $adminUser->getRole()) {
            $isUserGranted = $this->adminUserAccessChecker->isUserGranted($adminUser, $routeName);

            if (!$isUserGranted) {
                $event->setResponse($this->redirectUser($this->getRedirectRoute(), $this->getRedirectMessage()));
            }
        }
    }

    private function getRedirectRoute(): string
    {
        return  $this->router->generate('sylius_admin_dashboard');
    }

    private function getRedirectMessage(): string
    {
        return  'arobases_sylius_rights_management.message.access_denied';
    }

    protected function redirectUser(string $route, string $message): RedirectResponse
    {
        $this->requestStack->getSession()->getFlashBag()->add('error', $message);

        return new RedirectResponse($route);
    }
}
