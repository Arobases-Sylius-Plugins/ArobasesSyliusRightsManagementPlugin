<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Command;

use Arobases\SyliusRightsManagementPlugin\Adapter\RightAdapter;
use Arobases\SyliusRightsManagementPlugin\Entity\Right;
use Arobases\SyliusRightsManagementPlugin\Entity\RightGroup;
use Arobases\SyliusRightsManagementPlugin\Entity\Role;
use Arobases\SyliusRightsManagementPlugin\Repository\Group\RightGroupRepository;
use Arobases\SyliusRightsManagementPlugin\Repository\Right\RightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminRightCommand extends Command
{
    protected static $defaultName = 'arobases:right:create-admin-right';

    protected EntityManagerInterface $manager;

    protected RightAdapter $rightAdapter;

    protected RightGroupRepository $groupRightRepository;

    protected RightRepository $rightRepository;

    protected string $defaultAdminUser;
    protected string $defaultAdminRoleCode;
    protected string $defaultAdminRoleName;

    public function __construct(
        EntityManagerInterface $manager,
        RightAdapter $rightAdapter,
        RightGroupRepository $groupRightRepository,
        RightRepository $rightRepository,
        string $defaultAdminUser,
        string $defaultAdminRoleCode,
        string $defaultAdminRoleName
    ) {
        $this->manager = $manager;
        $this->rightAdapter = $rightAdapter;
        $this->groupRightRepository = $groupRightRepository;
        $this->rightRepository = $rightRepository;
        $this->defaultAdminUser = $defaultAdminUser;
        $this->defaultAdminRoleCode = $defaultAdminRoleCode;
        $this->defaultAdminRoleName = $defaultAdminRoleName;
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $administratorRole = null;
        $defaultAdminUser = $this->defaultAdminUser;
        $defaultAdminRoleCode = $this->defaultAdminRoleCode;
        $defaultAdminRoleName = $this->defaultAdminRoleName;
        if ($defaultAdminUser && $defaultAdminRoleCode && $defaultAdminRoleName) {
            $adminUser = $this->manager->getRepository(AdminUserInterface::class)->findOneBy(['username' => $defaultAdminUser]);
            if ($adminUser) {
                $administratorRole = $this->manager->getRepository(Role::class)->findOneBy(['code' => $defaultAdminRoleCode]);
                if (!$administratorRole) {
                    $administratorRole = new Role();
                    $administratorRole->setCode($defaultAdminRoleCode);
                    $administratorRole->setName($defaultAdminRoleName);
                }
                $adminUser->setRole($administratorRole);
            }
        }

        $arrayRights = $this->rightAdapter->getRightsFromYaml();

        foreach ($arrayRights as $group => $values) {
            /** @var RightGroup $rightGroup */
            $rightGroup = $this->groupRightRepository->findOneBy(['name' => $group]);
            if (!$rightGroup) {
                $rightGroup = new RightGroup();
            }
            $rightGroup->setName($group);
            $this->manager->persist($rightGroup);
            $this->manager->flush();

            if (!array_key_exists('rights', $values)) {
                continue;
            }
            foreach ($values['rights'] as $key => $value) {
                $right = $this->rightRepository->findOneBy(['name' => $value['name']]);
                if (!$right) {
                    $right = new Right();
                }
                $right->setName($value['name']);
                $right->setRoutes($value['routes']);
                $right->setExcludedRoutes($value['excludes']);
                $right->setRightGroup($rightGroup);
                if ($administratorRole) {
                    $right->addRole($administratorRole);
                }

                $this->manager->persist($right);
            }
        }
        $this->manager->flush();

        return Command::SUCCESS;
    }
}
