<?php

namespace UserBundle\Command;

use UserBundle\Doctrine\UserManager;
use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeactivateUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'user:deactivate';

    protected function configure()
    {
        $this
            ->setDescription('Deactivate a user')
            ->addArgument('username', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        /** * @var UserManager $userManager */
        $userManager = $this->getContainer()->get(UserManager::class);

        /** @var User $user */
        $user = $userManager->findOneBy(['username' => $username]);
        if (!$user) {
            throw new \Exception(sprintf('User %s not exists!', $username));
        }

        $user->setEnabled(false);
        $userManager->update($user);

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('User "%s" has been deactivated.', $username));
    }
}
