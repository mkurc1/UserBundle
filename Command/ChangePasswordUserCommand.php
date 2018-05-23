<?php

namespace App\UserBundle\Command;

use App\UserBundle\Doctrine\UserManager;
use App\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangePasswordUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'user:change-password';

    protected function configure()
    {
        $this
            ->setDescription('Change the password of a user')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        /** * @var UserManager $userManager */
        $userManager = $this->getContainer()->get(UserManager::class);

        /** @var User $user */
        $user = $userManager->findOneBy(['username' => $username]);
        if (!$user) {
            throw new \Exception(sprintf('User %s not exists!', $username));
        }

        $user->setPassword($userManager->encodePassword($user, $password));
        $userManager->update($user);

        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('Changed password for user %s', $username));
    }
}
