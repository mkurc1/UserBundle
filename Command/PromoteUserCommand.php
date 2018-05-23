<?php

namespace UserBundle\Command;

use UserBundle\Doctrine\UserManager;
use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PromoteUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'user:promote';

    protected function configure()
    {
        $this
            ->setDescription('Promote a user by adding a role')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('role', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        /** * @var UserManager $userManager */
        $userManager = $this->getContainer()->get(UserManager::class);

        /** @var User $user */
        $user = $userManager->findOneBy(['username' => $username]);
        if (!$user) {
            throw new \Exception(sprintf('User %s not exists!', $username));
        }

        if ($user->hasRole($role)) {
            throw new \Exception(sprintf('User "%s" did already have "%s" role.', $username, $role));
        }

        $user->addRole($role);
        $userManager->update($user);

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('Role %s has been added to user %s.', $role, $username));
    }
}
